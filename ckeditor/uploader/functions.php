<?php

define('HTTPS', isset($_SERVER['HTTPS']) && filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN));
define('STORAGE_PATH', './storage');

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

function parse_filesize($B, $D = 2) {
    $S = 'KMGTPEZY';
    $F = floor((strlen($B) - 1) / 3);
    return [
        'size' => number_format($B/pow(1024, $F), $D),
        'unit' => @$S[$F-1].'B'
    ];
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

function storagePath() {
    $protocol = HTTPS ? 'https' : 'http';
    return "{$protocol}://{$_SERVER['HTTP_HOST']}/ckeditor/uploader/storage";
}

function getFileInfo($path, $rootPath = '') {
    $stat = stat($path);
    $info = pathinfo($path);
    $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    $protocol = HTTPS ? 'https' : 'http';

    if (preg_match("#^image\/.*#", $mime)) {
        $rootPath = $rootPath ? rtrim($rootPath, '\\/') : "{$protocol}://{$_SERVER['HTTP_HOST']}/ckeditor/uploader";
        $src = "{$rootPath}/".trim($path, './');
    }

    return [
        'src'       => $src,
        'size'      => format_filesize($stat['size']),
        'accessed'  => date('Y-m-d H:i:s', $stat['atime']),
        'modified'  => date('Y-m-d H:i:s', $stat['mtime']),
        'created'   => date('Y-m-d H:i:s', $stat['ctime']),
        'filename'  => $info['filename'],
        'basename'  => $info['basename'],
        'extension' => $info['extension'],
        'mime'      => $mime,
    ];
}

function getExtensionFromUrl($url) {
    $url = str_replace('\\', '/', $url);
    $url = explode('/', $url);
    $extension = explode('.', array_pop($url));
    if (count($extension) > 1) {
        return array_pop($extension);
    }
    return false;
}

function getUniqueString() {
    return time() . rand();
}

function move_file($file, $to) {
    $info = pathinfo($file);
    $to   = "$to/{$info['basename']}";
    return rename($file, $to);
    // if (copy($file, $to)) {
    //     return unlink($file);
    // }
    // return false;
}
