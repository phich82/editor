<?php
include_once './functions.php';

if (isset($_POST['path'])) {
    $pathRequest = trim($_POST['path'], '\/\\');
    $path = './storage'.DIRECTORY_SEPARATOR.$pathRequest;

    if (!is_dir($path) && !file_exists($path)) {
        return responseJson([
            'success' => false,
            'error' => "Folder [$pathRequest] not exists.",
            'data' => null,
        ]);
    }

    $files = scandir($path);
    if ($files === false) {
        return responseJson([
            'success' => false,
            'error' => 'Could not get files.',
            'data' => null,
        ]);
    }

    $filesExcluded = [
        '.',
        '..',
        '.DS_Store',
        '.history',
        '.vscode',
        '.sonarlint',
    ];
    // Only get files in folder (not included files in subfolders)
    $files = array_reduce($files, function ($carry, $file) use ($filesExcluded, $path, $pathRequest) {
        if (!in_array($file, $filesExcluded) && is_file("{$path}/{$file}")) {
            $stat = stat("{$path}/{$file}");
            $info = pathinfo("{$path}/{$file}");
            $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), "{$path}/{$file}");

            if (preg_match("#^image\/.*#", $mime)) {
                $src = 'http://'.$_SERVER['HTTP_HOST'].'/ckeditor/uploader/'.trim("{$path}/{$file}", './');
            } else {
                $src = 'http://'.$_SERVER['HTTP_HOST'].'/ckeditor/uploader/images/'."{$info['extension']}.png";
            }
            $carry[] = [
                'src'       => $src,
                'folder'    => $pathRequest,
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
        return $carry;
    }, []);

    return responseJson([
        'success' => true,
        'data' => $files,
    ]);
}

return responseJson([
    'success' => false,
    'error' => 'Missing `path` key.',
    'data' => null,
]);
