<?php

function dd($value) {
    echo '<pre>'.json_encode($value, JSON_PRETTY_PRINT).'</pre>';
    exit;
}

function responseJson($data) {
    echo json_encode($data);
    exit;
}

function format_filesize($B, $D = 2) {
    $S = 'KMGTPEZY';
    $F = floor((strlen($B) - 1) / 3);
    return sprintf("%.{$D}f", $B/pow(1024, $F)).' '.@$S[$F-1].'B';
}

function createId($str) {
    return preg_replace('#\s+#', '_', strtolower(trim($str)));
}

function scanDirectory($base_dir = '../uploader/storage', $level = 0) {
    $directories = [];
    foreach (scandir($base_dir) as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        $dir = $base_dir.DIRECTORY_SEPARATOR.$file;
        if (is_dir($dir)) {
            $directories[] = [
                'level'    => $level,
                'name'     => $file,
                'path'     => preg_replace('#^\.\.\/uploader\/storage#', '', $dir),
                'children' => scanDirectory($dir, $level +1)
            ];
        }
    }
    return $directories;
}
