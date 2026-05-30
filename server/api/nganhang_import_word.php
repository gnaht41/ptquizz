<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/Database.php";
require_once __DIR__ . "/../model/giangvien/nganhang.model.php";

require_once __DIR__ . "/../vendor/autoload.php";

$user = Api::requireRole(["admin", "giangvien"]);

// Bẫy lỗi để trả về JSON thay vì HTML
error_reporting(E_ALL);
ini_set('display_errors', 0);
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) return;
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

$id_nhch = isset($_POST["id_nhch"]) ? (int) $_POST["id_nhch"] : 0;
if ($id_nhch <= 0) {
    Api::json(["error" => "Thiếu ID ngân hàng câu hỏi"], 400);
}

if (!isset($_FILES["word_file"]) || !is_array($_FILES["word_file"])) {
    Api::json(["error" => "Vui lòng chọn file (.docx, .xlsx hoặc .pdf)"], 400);
}

$file = $_FILES["word_file"];
if (($file["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    Api::json(["error" => "Upload file thất bại"], 400);
}

$extension = strtolower(pathinfo((string) ($file["name"] ?? ""), PATHINFO_EXTENSION));
if ($extension !== "docx" && $extension !== "xlsx" && $extension !== "pdf") {
    Api::json(["error" => "Chỉ hỗ trợ file định dạng .docx, .xlsx hoặc .pdf"], 400);
}

// Validate ownership
$bank = getQuestionBankById($id_nhch, (int) ($user["id_nguoidung"] ?? 0), $user["vaitro"] ?? "");
if (!$bank) {
    Api::json(["error" => "Bạn không có quyền import vào ngân hàng này"], 403);
}

function parse_docx_text($path)
{
    $content = false;
    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        if ($zip->open($path) === true) {
            $content = $zip->getFromName("word/document.xml");
            $zip->close();
        }
    }
    if (!$content) $content = read_zip_entry_manual($path, 'word/document.xml');
    if (!$content) throw new Exception("Không thể đọc file Word.");

    $content = preg_replace('/<w:p[^>]*>/', "\n", $content);
    $content = preg_replace('/<w:tab[^>]*\/>/', "\t", $content);
    $content = preg_replace('/<w:br[^>]*\/>/', "\n", $content);
    $text = strip_tags($content);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    return clean_lines_to_array($text);
}

function parse_xlsx_text($path)
{
    $sharedStrings = [];
    $isZip = class_exists('ZipArchive');
    
    $fetchXml = function($zipPath) use ($path, $isZip) {
        if ($isZip) {
            $zip = new ZipArchive();
            if ($zip->open($path) === true) {
                // Thử cả hoa và thường
                $data = $zip->getFromName($zipPath) ?: $zip->getFromName(str_replace('s', 'S', $zipPath));
                $zip->close();
                return $data;
            }
        }
        return read_zip_entry_manual($path, $zipPath) ?: read_zip_entry_manual($path, str_replace('s', 'S', $zipPath));
    };

    // 1. Get Shared Strings
    $ssXml = $fetchXml("xl/sharedStrings.xml");
    if ($ssXml) {
        if (preg_match_all('/<si>(.*?)<\/si>/is', $ssXml, $siMatches)) {
            foreach ($siMatches[1] as $si) {
                preg_match_all('/<t[^>]*>(.*?)<\/t>/is', $si, $tMatches);
                $sharedStrings[] = implode('', $tMatches[1]);
            }
        }
    }

    // 2. Get Sheet1 (Thử nhiều đường dẫn phổ biến)
    $sheetXml = $fetchXml("xl/worksheets/sheet1.xml") ?: $fetchXml("xl/worksheets/Sheet1.xml");
    if (!$sheetXml) throw new Exception("Không thể tìm thấy nội dung Sheet 1 trong file Excel.");

    // 3. Extract Cells
    preg_match_all('/<c[^>]*>(.*?)<\/c>/is', $sheetXml, $cMatches);
    
    $lines = [];
    foreach ($cMatches[0] as $cTag) {
        // Trường hợp Inline String: <is><t>...</t></is>
        if (preg_match('/<t[^>]*>(.*?)<\/t>/is', $cTag, $tMatch)) {
            $lines[] = html_entity_decode($tMatch[1], ENT_QUOTES | ENT_XML1, 'UTF-8');
            continue;
        }

        // Trường hợp dùng Shared Strings hoặc Number: <v>...</v>
        if (preg_match('/<v[^>]*>(.*?)<\/v>/is', $cTag, $vMatch)) {
            $val = $vMatch[1];
            if (preg_match('/t="s"/i', $cTag)) {
                $lines[] = html_entity_decode($sharedStrings[(int)$val] ?? '', ENT_QUOTES | ENT_XML1, 'UTF-8');
            } else {
                $lines[] = $val;
            }
        }
    }

    $result = clean_lines_to_array(implode("\n", $lines));
    if (empty($result)) {
         throw new Exception("File Excel không có dữ liệu ở Sheet 1 hoặc định dạng không tương thích. Raw XML length: " . strlen($sheetXml));
    }
    return $result;
}

function parse_pdf_text($path)
{
    try {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();
        return clean_lines_to_array($text);
    } catch (Exception $e) {
        throw new Exception("Lỗi đọc file PDF: " . $e->getMessage());
    }
}

function clean_lines_to_array($text)
{
    $lines = preg_split("/\r\n|\n|\r/u", (string)$text);
    return array_values(array_filter(array_map(static function ($line) {
        return trim(preg_replace('/\s+/u', ' ', (string) $line));
    }, $lines), static fn($line) => $line !== ''));
}

function read_zip_entry_manual($path, $targetName)
{
    if (!function_exists('gzinflate')) return false;
    $fp = @fopen($path, 'rb');
    if (!$fp) return false;
    while (!feof($fp)) {
        $sig = fread($fp, 4);
        if ($sig !== "PK\x03\x04") break;
        $header = fread($fp, 26);
        if (strlen($header) < 26) break;
        
        // Đặt tên cho tất cả các field để tránh lỗi "Undefined array key"
        $p = unpack('vver/vflag/vmethod/vtime/vdate/Vcrc/VcompSize/VuncompSize/vnameLen/vextraLen', $header);
        $name = fread($fp, (int) $p['nameLen']);
        if ($p['extraLen']) fseek($fp, (int) $p['extraLen'], SEEK_CUR);
        
        $data = fread($fp, (int) $p['compSize']);
        if ($name === $targetName) {
            fclose($fp);
            return @gzinflate($data) ?: $data;
        }
    }
    fclose($fp);
    return false;
}

function parse_questions_from_lines($lines)
{
    $questions = [];
    $current = null;
    $flushCurrent = static function (&$current, &$questions) {
        if (!$current) return;
        
        // Xác định loại câu hỏi
        $isFillInBlank = empty($current['options']);

        if (empty($current['noidungcauhoi'])) {
             return;
        }

        if (empty($current['loigiai_chitiet'])) {
            throw new Exception("Câu hỏi sau đây chưa có 'Lời giải:': " . $current['noidungcauhoi']);
        }

        $dapan_list = [];
        $loai_cauhoi = 1; // Mặc định trắc nghiệm

        if ($isFillInBlank) {
            $loai_cauhoi = 2; // Điền từ
            if (empty($current['answer_raw'])) {
                throw new Exception("Câu hỏi điền từ thiếu đáp án: " . $current['noidungcauhoi']);
            }
            
            // Tách các đáp án bằng dấu |
            $rawAnswers = explode('|', $current['answer_raw']);
            foreach ($rawAnswers as $idx => $rawAns) {
                $dapan_list[] = [
                    'noidung' => trim($rawAns),
                    'dapandung' => 1,
                    'loigiai_chitiet' => ($idx === 0) ? $current['loigiai_chitiet'] : null
                ];
            }
        } else {
            if (empty($current['answer_letter'])) {
                throw new Exception("Câu hỏi trắc nghiệm thiếu 'Đáp án: [Chữ cái]': " . $current['noidungcauhoi']);
            }
            $answerIndex = ord($current['answer_letter']) - 65;
            if (!isset($current['options'][$answerIndex])) {
                throw new Exception("Đáp án đúng '{$current['answer_letter']}' không khớp với danh sách A/B/C/D của câu: " . $current['noidungcauhoi']);
            }
            foreach ($current['options'] as $index => $optionText) {
                $dapan_list[] = [
                    'noidung' => $optionText,
                    'dapandung' => $index === $answerIndex ? 1 : 0,
                    'loigiai_chitiet' => ($index === $answerIndex) ? $current['loigiai_chitiet'] : null
                ];
            }
        }

        $questions[] = [
            'noidungcauhoi' => $current['noidungcauhoi'], 
            'dokho' => $current['dokho'] ?: 'Dễ', 
            'loai_cauhoi' => $loai_cauhoi,
            'dapan_list' => $dapan_list
        ];
        $current = null;
    };

    foreach ($lines as $line) {
        // Hỗ trợ cả "Câu 1:" và "Câu 1."
        if (preg_match('/^Câu\s*\d+\s*[:\.]\s*(.+)$/iu', $line, $matches)) {
            $flushCurrent($current, $questions);
            $current = ['noidungcauhoi' => trim($matches[1]), 'options' => [], 'answer_letter' => '', 'answer_raw' => '', 'dokho' => 'Dễ', 'loigiai_chitiet' => null];
            continue;
        }
        if (!$current) continue;
        if (preg_match('/^([A-D])\.\s*(.+)$/u', $line, $matches)) {
            $current['options'][ord($matches[1]) - 65] = trim($matches[2]);
            continue;
        }
        if (preg_match('/^Đáp án\s*[:\.]\s*(.+)$/iu', $line, $matches)) {
            $val = trim($matches[1]);
            if (strlen($val) === 1 && preg_match('/^[A-D]$/i', $val)) {
                $current['answer_letter'] = strtoupper($val);
            }
            $current['answer_raw'] = $val;
            continue;
        }
        if (preg_match('/^Độ khó\s*[:\.]\s*(Dễ|Trung bình|Khó)$/iu', $line, $matches)) {
            $current['dokho'] = $matches[1];
            continue;
        }
        if (preg_match('/^Lời giải\s*[:\.]\s*(.+)$/iu', $line, $matches)) {
            $current['loigiai_chitiet'] = trim($matches[1]);
            continue;
        }
        $current['noidungcauhoi'] .= ' ' . trim($line);
    }
    $flushCurrent($current, $questions);
    return $questions;
}

try {
    if ($extension === 'docx') {
        $lines = parse_docx_text($file["tmp_name"]);
    } elseif ($extension === 'xlsx') {
        $lines = parse_xlsx_text($file["tmp_name"]);
    } else {
        $lines = parse_pdf_text($file["tmp_name"]);
    }
    $questions = parse_questions_from_lines($lines);
    if (!$questions) {
        $sample = array_slice($lines, 0, 10);
        throw new Exception("Không tìm thấy câu hỏi hợp lệ. Hệ thống đọc được 10 dòng đầu là: " . implode(" | ", $sample));
    }
    
    $result = createManyInBank($id_nhch, $questions);
    if (!($result["success"] ?? false)) throw new Exception($result["message"] ?? "Không thể import câu hỏi");
    
    Api::json(["success" => true, "message" => "Import thành công " . (int) ($result["count"] ?? count($questions)) . " câu hỏi vào ngân hàng từ " . strtoupper($extension)]);
} catch (Exception $e) {
    Api::json(["error" => $e->getMessage()], 400);
}
