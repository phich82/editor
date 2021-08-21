<?php
include_once './functions.php';

if (isset($_POST['folder'])) {
    $subfolderRequest = trim($_POST['folder'], '\/\\');
    $path = './storage/'.$subfolderRequest;

    if (is_file($path)) {
        return responseJson([
            'success' => false,
            'error' => "Folder [$subfolderRequest] is not a folder.",
            'data' => null,
        ]);
    }

    if (file_exists($path)) {
        return responseJson([
            'success' => false,
            'error' => "Folder [$subfolderRequest] existed.",
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

