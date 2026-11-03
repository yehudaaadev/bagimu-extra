<?php

namespace App\Support;

class Barcode
{
    /**
     * Code 128 pattern dictionary (Code B subset).
     */
    private static array $patterns = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213', // 0-9
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132', // 10-19
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211', // 20-29
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313', // 30-39
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331', // 40-49
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111', // 50-59
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214', // 60-69
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111', // 70-79
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141', // 80-89
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141', // 90-99
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112' // 100-106
    ];

    /**
     * Generate an SVG Code 128 barcode string.
     */
    public static function svg(string $text, int $height = 32, int $barWidth = 2, string $color = '#0f172a'): string
    {
        $text = trim($text);
        if ($text === '') {
            $text = '0';
        }

        $codes = [104]; // Start Code B
        $checksum = 104;

        $len = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            $val = ord($text[$i]) - 32;
            if ($val < 0 || $val > 95) {
                $val = 0;
            }
            $codes[] = $val;
            $checksum += $val * ($i + 1);
        }

        $codes[] = $checksum % 103;
        $codes[] = 106; // Stop Code

        $modules = '';
        foreach ($codes as $code) {
            $pattern = self::$patterns[$code];
            $isBar = true;
            for ($p = 0; $p < strlen($pattern); $p++) {
                $w = (int) $pattern[$p];
                $modules .= str_repeat($isBar ? '1' : '0', $w);
                $isBar = ! $isBar;
            }
        }
        $modules .= '11'; // Stop terminator bar

        $totalModules = strlen($modules);
        $totalWidth = $totalModules * $barWidth;

        $rects = [];
        $x = 0;
        for ($i = 0; $i < $totalModules; $i++) {
            if ($modules[$i] === '1') {
                $rects[] = sprintf('<rect x="%d" y="0" width="%d" height="%d" fill="%s" />', $x, $barWidth, $height, htmlspecialchars($color));
            }
            $x += $barWidth;
        }

        return sprintf(
            '<svg viewBox="0 0 %d %d" width="100%%" height="100%%" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" style="display:block;">%s</svg>',
            $totalWidth,
            $height,
            implode('', $rects)
        );
    }
}
