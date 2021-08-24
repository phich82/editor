<?php
include_once './functions.php';

if (!isset($_POST['action'])) {
    return responseJson([
        'success' => false,
        'error' => 'Missing `action` key.',
        'data' => null,
    ]);
}

if (!isset($_POST['folder'])) {
    return responseJson([
        'success' => false,
        'error' => 'Missing `folder` key.',
        'data' => null,
    ]);
}

$action = $_POST['action'];
$folderRequest = trim($_POST['folder'], '\/\\');


// Create a new folder
if ($action == 'create') {
    $path = './storage/'.$folderRequest;

    if (is_file($path)) {
        return responseJson([
            'success' => false,
            'error' => "Folder [$folderRequest] is not a folder.",
            'data' => null,
        ]);
    }

    if (file_exists($path)) {
        return responseJson([
            'success' => false,
            'error' => "Folder [$folderRequest] existed.",
            'data' => null,
        ]);
    }

    if (mkdir($path, 0777, true)) {
        return responseJson([
            'success' => true,
            'data' => $folderRequest,
        ]);
    }

    return responseJson([
        'success' => false,
        'error' => 'Could not create new folder.',
        'data' => null,
    ]);
}

// Rename folder
if ($action == 'rename') {
    if (!isset($_POST['old_folder'])) {
        return responseJson([
            'success' => false,
            'error' => 'Missing `old_folder` key.',
            'data' => null,
        ]);
    }

    $oldfolderRequest = trim($_POST['old_folder'], '\/\\');

    if (empty($oldfolderRequest)) {
        return responseJson([
            'success' => false,
            'error' => 'Old folder is empty.',
            'data' => null,
        ]);
    }

    $pathOldFolder = './storage/'.$oldfolderRequest;

    if (is_file($pathOldFolder)) {
        return responseJson([
            'success' => false,
            'error' => "Folder [$folderRequest] is not a folder.",
            'data' => null,
        ]);
    }

    if (!file_exists($pathOldFolder)) {
        return responseJson([
            'success' => false,
            'error' => "Old folder [$pathOldFolder] not exists.",
            'data' => null,
        ]);
    }

    // Only get parent folder
    $partsOldFolder = explode(DIRECTORY_SEPARATOR, $pathOldFolder);
    array_pop($partsOldFolder);

    // New folder name
    $pathNewFolderName = implode(DIRECTORY_SEPARATOR, $partsOldFolder) . DIRECTORY_SEPARATOR . $folderRequest;

    if (file_exists($pathNewFolderName)) {
        return responseJson([
            'success' => false,
            'error' => "Folder [$folderRequest] existed.",
            'data' => null,
        ]);
    }

    if (rename($pathOldFolder, $pathNewFolderName)) {
        $newFolderPath = explode('/', $oldfolderRequest);
        array_pop($newFolderPath);
        $newFolderPath = implode('/', $newFolderPath);
        return responseJson([
            'success' => true,
            'data' => [
                'path' => $newFolderPath.DIRECTORY_SEPARATOR.$folderRequest,
                'name' => $folderRequest,
            ],
        ]);
    }

    return responseJson([
        'success' => false,
        'error' => 'Could not rename folder.',
        'data' => null,
    ]);
}

// Delete folder
if ($action == 'delete') {
    $pathFolder = './storage/'.$folderRequest;

    if (!file_exists($pathFolder)) {
        return responseJson([
            'success' => false,
            'error' => "Folder [$folderRequest] not exist.",
            'data' => null,
        ]);
    }
    if (rmdir($pathFolder)) {
        return responseJson([
            'success' => true,
            'data' => $folderRequest,
        ]);
    }
    return responseJson([
        'success' => false,
        'error' => 'Could not delete folder.',
        'data' => null,
    ]);
}

return responseJson([
    'success' => false,
    'error' => "Action [$action] not be allowed.",
    'data' => null,
]);
