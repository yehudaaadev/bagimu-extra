<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExport
{
    /**
     * Excel SpreadsheetML 2003 (.xls) tanpa dependensi — bisa dibuka
     * MS Excel/LibreOffice. Netralisir formula-injection seperti CSV.
     */
    public static function xls(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        $cell = function ($v): string {
            $s = (string) ($v ?? '');
            if (preg_match('/^[=+\-@\t\r]/', $s)) {
                $s = "'".$s;
            }

            return '<Cell><Data ss:Type="String">'.htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8').'</Data></Cell>';
        };

        return response()->streamDownload(function () use ($headers, $rows, $cell) {
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
            echo '<Worksheet ss:Name="Pendaftar"><Table>';
            echo '<Row>';
            foreach ($headers as $h) {
                echo $cell($h);
            }
            echo '</Row>';
            foreach ($rows as $row) {
                $row = is_array($row) ? $row : $row->toArray();
                echo '<Row>';
                foreach ($row as $v) {
                    echo $cell($v);
                }
                echo '</Row>';
            }
            echo '</Table></Worksheet></Workbook>';
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel']);
    }

    /**
     * Excel SpreadsheetML 2003 Multi-Sheet (.xls) tanpa dependensi pihak ketiga.
     * Setiap sheet dipisahkan oleh tag <Worksheet ss:Name="...">.
     *
     * @param  array<string, array{headers: array, rows: iterable}>  $sheets
     */
    public static function multiSheetXls(string $filename, array $sheets): StreamedResponse
    {
        $cell = function ($v): string {
            $s = (string) ($v ?? '');
            if (preg_match('/^[=+\-@\t\r]/', $s)) {
                $s = "'".$s;
            }

            return '<Cell><Data ss:Type="String">'.htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8').'</Data></Cell>';
        };

        return response()->streamDownload(function () use ($sheets, $cell) {
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';

            foreach ($sheets as $sheetName => $sheetData) {
                $safeName = htmlspecialchars(substr((string) $sheetName, 0, 31), ENT_QUOTES | ENT_XML1, 'UTF-8');
                echo '<Worksheet ss:Name="'.$safeName.'"><Table>';

                if (! empty($sheetData['headers'])) {
                    echo '<Row>';
                    foreach ($sheetData['headers'] as $h) {
                        echo $cell($h);
                    }
                    echo '</Row>';
                }

                if (! empty($sheetData['rows'])) {
                    foreach ($sheetData['rows'] as $row) {
                        $row = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array) $row);
                        echo '<Row>';
                        foreach ($row as $v) {
                            echo $cell($v);
                        }
                        echo '</Row>';
                    }
                }

                echo '</Table></Worksheet>';
            }

            echo '</Workbook>';
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }
}
