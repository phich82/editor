<?php
include_once './functions.php';

if (isset($_POST['folder'])) {
    $subfolderRequest = trim($_POST['folder'], '\/\\');
    $path = './storage'.DIRECTORY_SEPARATOR.$subfolderRequest;

    if (is_dir($path)) {
        return responseJson([
            'success' => false,
            'error' => "Folder [$pathRequest] existed.",
            'data' => null,
        ]);
    }

    $success = mkdir($path, 0777, true);

    return responseJson([
        'success' => $success,
        'data' => $subfolderRequest,
    ]);
}

return responseJson([
    'success' => false,
    'error' => 'Missing `folder` key.',
    'data' => null,
]);

