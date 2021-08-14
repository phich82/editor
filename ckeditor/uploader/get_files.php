<?php

function responseJson($data) {
    echo json_encode($data);
    exit;
}

if (isset($_POST['path'])) {
    $pathRequest = trim($_POST['path'], '/');
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
    $files = array_reduce($files, function ($carry, $file) use ($filesExcluded, $path) {
        if (!in_array($file, $filesExcluded) && is_file("{$path}/{$file}")) {
            $carry[] = 'http://'.$_SERVER['HTTP_HOST'].'/ckeditor/uploader/'.trim("{$path}/{$file}", './');
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