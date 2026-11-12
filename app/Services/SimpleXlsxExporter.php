<?php

namespace App\Services;

use ZipArchive;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class SimpleXlsxExporter
 *
 * A lightweight, zero-dependency OpenXML (.xlsx) spreadsheet generator.
 * Creates professionally styled Excel workbooks with:
 *   • Dynamic column widths scaled to character length (no truncated text or '###' cells)
 *   • Elegant non-flashy corporate styling (slate headers, zebra striping, crisp gridlines)
 *   • Title banner, class information metadata, and summary rows
 *   • Fast streaming with low memory footprint
 */
class SimpleXlsxExporter
{
    protected string $title = '';
    protected array $metadata = [];
    protected array $headers = [];
    protected array $rows = [];
    protected array $summaryRow = [];
    protected string $sheetName = 'Student Report';

    public static function make(): self
    {
        return new self();
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setSheetName(string $name): self
    {
        $this->sheetName = substr(preg_replace('/[\\\\\/\?\*\[\]]/', '', $name), 0, 31) ?: 'Report';
        return $this;
    }

    public function addMetadata(string $label, string $value): self
    {
        $this->metadata[] = ['label' => $label, 'value' => $value];
        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function addRow(array $row): self
    {
        $this->rows[] = array_values($row);
        return $this;
    }

    public function addRows(array $rows): self
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
        return $this;
    }

    public function setSummaryRow(array $summaryRow): self
    {
        $this->summaryRow = array_values($summaryRow);
        return $this;
    }

    /**
     * Convert a 1-based column number into Excel column letters (1 -> A, 27 -> AA).
     */
    protected function colLetter(int $colIndex): string
    {
        $letter = '';
        while ($colIndex > 0) {
            $mod = ($colIndex - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $colIndex = (int)(($colIndex - $mod) / 26);
        }
        return $letter;
    }

    /**
     * Escape strings for safe OpenXML inclusion.
     */
    protected function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
     * Calculate optimal character scale width for each column.
     * Evaluates header lengths and all row values to guarantee no text truncation.
     */
    protected function calculateColumnWidths(): array
    {
        $widths = [];
        $colCount = count($this->headers);

        for ($i = 0; $i < $colCount; $i++) {
            $headerText = (string)($this->headers[$i] ?? '');
            $maxLen = mb_strlen($headerText, 'UTF-8');

            foreach ($this->rows as $row) {
                $cellVal = isset($row[$i]) ? (string)$row[$i] : '';
                $len = mb_strlen($cellVal, 'UTF-8');
                if ($len > $maxLen) {
                    $maxLen = $len;
                }
            }

            if (!empty($this->summaryRow) && isset($this->summaryRow[$i])) {
                $sumLen = mb_strlen((string)$this->summaryRow[$i], 'UTF-8');
                if ($sumLen > $maxLen) {
                    $maxLen = $sumLen;
                }
            }

            // Character scale formula:
            // Excel character width is based on standard 11pt font width.
            // Add 4.5 units of padding for cell margins, borders, and filter arrows.
            $computed = $maxLen + 4.5;

            // Give specific columns tailored minimums
            if ($i === 0) {
                $computed = max(7.0, $computed); // 'No' column
            } elseif ($i === 1) {
                $computed = max(22.0, $computed); // 'Student Name'
            } else {
                $computed = max(11.0, $computed);
            }

            // Cap at 55 to avoid unreasonably wide columns
            $widths[$i + 1] = round(min(55.0, $computed), 2);
        }

        return $widths;
    }

    /**
     * Generate styles.xml defining modern, elegant fonts, fills, and borders.
     * Color palette: Clean Slate (#1E293B), neutral gray gridlines (#CBD5E1), subtle zebra (#F8FAFC).
     */
    protected function buildStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'

            // Number formats
            . '<numFmts count="1">'
            . '<numFmt numFmtId="164" formatCode="#,##0.00"/>'
            . '</numFmts>'

            // Fonts:
            // 0 = Normal Data (10.5pt Segoe UI, Dark Slate)
            // 1 = Bold Header (11pt Segoe UI, White)
            // 2 = Sheet Title (14pt Bold Segoe UI, Dark Slate)
            // 3 = Subtitle / Meta (9.5pt Segoe UI, Muted Slate)
            // 4 = Summary Row Bold (10.5pt Bold Segoe UI)
            . '<fonts count="5">'
            . '<font><sz val="10.5"/><color rgb="FF1E293B"/><name val="Segoe UI"/><family val="2"/></font>'
            . '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Segoe UI"/><family val="2"/></font>'
            . '<font><b/><sz val="14"/><color rgb="FF0F172A"/><name val="Segoe UI"/><family val="2"/></font>'
            . '<font><sz val="9.5"/><color rgb="FF64748B"/><name val="Segoe UI"/><family val="2"/></font>'
            . '<font><b/><sz val="10.5"/><color rgb="FF0F172A"/><name val="Segoe UI"/><family val="2"/></font>'
            . '</fonts>'

            // Fills:
            // 0 = None, 1 = Gray125 (Required by OpenXML spec)
            // 2 = Header Fill: Refined Dark Slate (#1E293B)
            // 3 = Zebra Row Fill: Very Subtle Slate (#F8FAFC)
            // 4 = Summary Fill: Soft Slate (#E2E8F0)
            . '<fills count="5">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF1E293B"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFE2E8F0"/></patternFill></fill>'
            . '</fills>'

            // Borders:
            // 0 = None
            // 1 = Thin Cell Border (#CBD5E1)
            // 2 = Header Border
            // 3 = Summary Border (thin top, double bottom)
            . '<borders count="4">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>'
            . '<border>'
            . '<left style="thin"><color rgb="FFCBD5E1"/></left>'
            . '<right style="thin"><color rgb="FFCBD5E1"/></right>'
            . '<top style="thin"><color rgb="FFCBD5E1"/></top>'
            . '<bottom style="thin"><color rgb="FFCBD5E1"/></bottom>'
            . '</border>'
            . '<border>'
            . '<left style="thin"><color rgb="FF334155"/></left>'
            . '<right style="thin"><color rgb="FF334155"/></right>'
            . '<top style="medium"><color rgb="FF0F172A"/></top>'
            . '<bottom style="medium"><color rgb="FF0F172A"/></bottom>'
            . '</border>'
            . '<border>'
            . '<left style="thin"><color rgb="FFCBD5E1"/></left>'
            . '<right style="thin"><color rgb="FFCBD5E1"/></right>'
            . '<top style="thin"><color rgb="FF475569"/></top>'
            . '<bottom style="double"><color rgb="FF0F172A"/></bottom>'
            . '</border>'
            . '</borders>'

            . '<cellStyleXfs count="1">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>'
            . '</cellStyleXfs>'

            // Cell Formats (s=0 to s=11):
            . '<cellXfs count="12">'
            // 0: Default
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            // 1: Sheet Title (14pt Bold)
            . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>'
            // 2: Meta info (9.5pt Muted)
            . '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>'
            // 3: Table Header (11pt Bold White, #1E293B bg, centered)
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            // 4: Data Left (White bg)
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1"><alignment horizontal="left" vertical="center"/></xf>'
            // 5: Data Center (White bg)
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>'
            // 6: Data Right / Number (White bg)
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1"><alignment horizontal="right" vertical="center"/></xf>'
            // 7: Data Left (Zebra #F8FAFC bg)
            . '<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="left" vertical="center"/></xf>'
            // 8: Data Center (Zebra #F8FAFC bg)
            . '<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>'
            // 9: Data Right / Number (Zebra #F8FAFC bg)
            . '<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="right" vertical="center"/></xf>'
            // 10: Summary Label (Bold, #E2E8F0 bg, double bottom border)
            . '<xf numFmtId="0" fontId="4" fillId="4" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="right" vertical="center"/></xf>'
            // 11: Summary Value (Bold, #E2E8F0 bg, double bottom border)
            . '<xf numFmtId="164" fontId="4" fillId="4" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="right" vertical="center"/></xf>'
            . '</cellXfs>'

            . '<cellStyles count="1">'
            . '<cellStyle name="Normal" xfId="0" builtinId="0"/>'
            . '</cellStyles>'
            . '</styleSheet>';
    }

    /**
     * Build the primary worksheet XML with auto-scaled column widths and formatted rows.
     */
    protected function buildSheetXml(): string
    {
        $colWidths = $this->calculateColumnWidths();
        $totalCols = count($this->headers);

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetViews><sheetView tabSelected="1" workbookViewId="0"><pane state="frozen" ySplit="5" topLeftCell="A6" activePane="bottomLeft"/></sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="18" customHeight="1"/>';

        // Column width definitions (Character scale)
        $xml .= '<cols>';
        foreach ($colWidths as $colIdx => $width) {
            $xml .= '<col min="' . $colIdx . '" max="' . $colIdx . '" width="' . $width . '" customWidth="1"/>';
        }
        $xml .= '</cols>';

        $xml .= '<sheetData>';
        $currentRow = 1;

        // 1. Title Row
        if ($this->title !== '') {
            $xml .= '<row r="' . $currentRow . '" ht="26" customHeight="1">';
            $xml .= '<c r="A' . $currentRow . '" t="inlineStr" s="1"><is><t>' . $this->xmlEscape($this->title) . '</t></is></c>';
            $xml .= '</row>';
            $currentRow++;
        }

        // 2. Metadata Rows (Class code, Date, Total Students)
        if (!empty($this->metadata)) {
            $metaTexts = [];
            foreach ($this->metadata as $meta) {
                $metaTexts[] = $meta['label'] . ': ' . $meta['value'];
            }
            $metaStr = implode('   •   ', $metaTexts);

            $xml .= '<row r="' . $currentRow . '" ht="18" customHeight="1">';
            $xml .= '<c r="A' . $currentRow . '" t="inlineStr" s="2"><is><t>' . $this->xmlEscape($metaStr) . '</t></is></c>';
            $xml .= '</row>';
            $currentRow++;
        }

        // 3. Spacing blank row
        $xml .= '<row r="' . $currentRow . '" ht="8" customHeight="1"/>';
        $currentRow++;

        // 4. Table Header Row (ht=26 for comfortable breathing room)
        $xml .= '<row r="' . $currentRow . '" ht="26" customHeight="1">';
        foreach ($this->headers as $idx => $header) {
            $colLetter = $this->colLetter($idx + 1);
            $cellRef = $colLetter . $currentRow;
            $xml .= '<c r="' . $cellRef . '" t="inlineStr" s="3"><is><t>' . $this->xmlEscape((string)$header) . '</t></is></c>';
        }
        $xml .= '</row>';
        $currentRow++;

        // 5. Data Rows
        foreach ($this->rows as $rIndex => $row) {
            $isZebra = ($rIndex % 2 === 1);
            $xml .= '<row r="' . $currentRow . '" ht="20" customHeight="1">';

            foreach ($row as $cIndex => $val) {
                $colLetter = $this->colLetter($cIndex + 1);
                $cellRef = $colLetter . $currentRow;

                // Pick alignment & background style:
                // Column 0 (No): Center
                // Column 1 (Student Name) & last (Class Grade): Left
                // All other columns (Scores, Totals, Averages): Right
                $isNumeric = is_numeric($val);
                $isCenter = ($cIndex === 0);
                $isRight = ($isNumeric && $cIndex > 1);

                if ($isCenter) {
                    $styleId = $isZebra ? 8 : 5;
                } elseif ($isRight) {
                    $styleId = $isZebra ? 9 : 6;
                } else {
                    $styleId = $isZebra ? 7 : 4;
                }

                if ($isNumeric) {
                    $xml .= '<c r="' . $cellRef . '" t="n" s="' . $styleId . '"><v>' . $val . '</v></c>';
                } else {
                    $xml .= '<c r="' . $cellRef . '" t="inlineStr" s="' . $styleId . '"><is><t>' . $this->xmlEscape((string)$val) . '</t></is></c>';
                }
            }

            $xml .= '</row>';
            $currentRow++;
        }

        // 6. Summary / Averages Row
        if (!empty($this->summaryRow)) {
            $xml .= '<row r="' . $currentRow . '" ht="22" customHeight="1">';
            foreach ($this->summaryRow as $cIndex => $val) {
                $colLetter = $this->colLetter($cIndex + 1);
                $cellRef = $colLetter . $currentRow;

                if (is_numeric($val)) {
                    $xml .= '<c r="' . $cellRef . '" t="n" s="11"><v>' . $val . '</v></c>';
                } else {
                    $xml .= '<c r="' . $cellRef . '" t="inlineStr" s="10"><is><t>' . $this->xmlEscape((string)$val) . '</t></is></c>';
                }
            }
            $xml .= '</row>';
        }

        $xml .= '</sheetData>';
        $xml .= '</worksheet>';

        return $xml;
    }

    /**
     * Package all OpenXML components into a temporary .xlsx zip file.
     * @return string Path to temporary file.
     */
    public function generateFile(): string
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'pointms_xlsx_');
        $zip = new ZipArchive();

        if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Failed to create spreadsheet temporary archive.');
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';

        // 2. _rels/.rels
        $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';

        // 4. xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>'
            . '<sheet name="' . $this->xmlEscape($this->sheetName) . '" sheetId="1" r:id="rId1"/>'
            . '</sheets>'
            . '</workbook>';

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rootRels);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);
        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/styles.xml', $this->buildStylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildSheetXml());
        $zip->close();

        return $tmpFile;
    }

    /**
     * Download the spreadsheet as a native .xlsx file response.
     */
    public function download(string $filename = 'report.xlsx'): StreamedResponse
    {
        if (!str_ends_with(strtolower($filename), '.xlsx')) {
            $filename .= '.xlsx';
        }

        $filePath = $this->generateFile();

        $headers = [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => (string)filesize($filePath),
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        return new StreamedResponse(function () use ($filePath) {
            $stream = fopen($filePath, 'rb');
            fpassthru($stream);
            fclose($stream);
            @unlink($filePath);
        }, 200, $headers);
    }
}

