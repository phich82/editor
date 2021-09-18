<?php
include_once './functions.php';

if (!isset($_POST['action'])) {
    return responseJson([
        'success' => false,
        'error' => 'Missing `action` key.',
        'data' => null,
    ]);
}

$action = $_POST['action'];

// Get files
if ($action == 'read' || $action == 'get') {
    // Validation
    if (!isset($_POST['path'])) {
        return responseJson([
            'success' => false,
            'error' => 'Missing `path` key.',
            'data' => null,
        ]);
    }

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
            $size = parse_filesize($stat['size']);
            if (preg_match("#^image\/.*#", $mime)) {
                $src = 'http://'.$_SERVER['HTTP_HOST'].'/ckeditor/uploader/'.trim("{$path}/{$file}", './');
            } else {
                $src = 'http://'.$_SERVER['HTTP_HOST'].'/ckeditor/uploader/images/'."{$info['extension']}.png";
            }
            $carry[] = [
                'src'           => $src,
                'folder'        => $pathRequest,
                'formated_size' => format_filesize($stat['size']),
                'size'          => (float) $size['size'],
                'size_unit'     => $size['unit'],
                'accessed'      => date('Y-m-d H:i:s', $stat['atime']),
                'modified'      => date('Y-m-d H:i:s', $stat['mtime']),
                'created'       => date('Y-m-d H:i:s', $stat['ctime']),
                'filename'      => $info['filename'],
                'basename'      => $info['basename'],
                'extension'     => $info['extension'],
                'mime'          => $mime,
            ];
        }
        return $carry;
    }, []);

    return responseJson([
        'success' => true,
        'data' => $files,
    ]);
}

// Rename file
if ($action == 'rename') {
    // Validation
    if (!isset($_POST['old_file'])) {
        return responseJson([
            'success' => false,
            'error' => 'Missing `old_file` key.',
            'data' => null,
        ]);
    }
    if (empty($_POST['old_file'])) {
        return responseJson([
            'success' => false,
            'error' => 'Old file is empty.',
            'data' => null,
        ]);
    }
    if (!isset($_POST['new_file'])) {
        return responseJson([
            'success' => false,
            'error' => 'Missing `new_file` key.',
            'data' => null,
        ]);
    }
    if (empty($_POST['new_file'])) {
        return responseJson([
            'success' => false,
            'error' => 'New file is empty.',
            'data' => null,
        ]);
    }

    $oldFile = $_POST['old_file'];
    $newFile = $_POST['new_file'];

    $oldFile = trim($oldFile, '\/\\');
    $oldFilePath = './storage'.DIRECTORY_SEPARATOR.$oldFile;

    if (!file_exists($oldFilePath)) {
        return responseJson([
            'success' => false,
            'error' => "Old file [{$oldFile}] not exists.",
            'data' => null,
        ]);
    }

    if (!is_file($oldFilePath)) {
        return responseJson([
            'success' => false,
            'error' => "Old file [{$oldFile}] is not a file.",
            'data' => null,
        ]);
    }
    $folderPath = dirname($oldFilePath);

    // Validate extension of image files (if new image file has not extension, getting extension of old image file)
    $extNewFile = getExtensionFromUrl($newFile);
    if ($extNewFile === false) {
        $extOldFile = getExtensionFromUrl($oldFilePath);

        if ($extOldFile === false) {
            return responseJson([
                'success' => false,
                'error' => "File format [{$oldFile}] could not be determined.",
                'data' => null,
            ]);
        }
        $newFile .= ".{$extOldFile}";
    }

    $success = rename($oldFilePath, $folderPath.DIRECTORY_SEPARATOR.$newFile);

    if (!$success) {
        return responseJson([
            'success' => false,
            'error' => "Could not rename filename.",
            'data' => null,
        ]);
    }

    $fileInfo = getFileInfo($folderPath.DIRECTORY_SEPARATOR.$newFile);

    return responseJson([
        'success' => true,
        'data' => array_merge(
            $fileInfo,
            [
                'path' => dirname($oldFile).DIRECTORY_SEPARATOR.$newFile
            ]
        ),
    ]);
}

// Delete file
if ($action == 'delete') {
    // Validation
    if (!isset($_POST['file'])) {
        return responseJson([
            'success' => false,
            'error' => 'Missing `file` key.',
            'data' => null,
        ]);
    }

    $pathFile = $_POST['file'];

    if (empty($pathFile)) {
        return responseJson([
            'success' => false,
            'error' => "File [$pathFile] is empty.",
            'data' => null,
        ]);
    }

    $pathFile = trim($pathFile, '\/\\');
    $pathFile = './storage'.DIRECTORY_SEPARATOR.$pathFile;

    if (!file_exists($pathFile)) {
        return responseJson([
            'success' => false,
            'error' => "File [$pathFile] not exists.",
            'data' => null,
        ]);
    }

    if (!is_file($pathFile)) {
        return responseJson([
            'success' => false,
            'error' => "File [$pathFile] is not a file.",
            'data' => null,
        ]);
    }

    $success = unlink($pathFile);

    if (!$success) {
        return responseJson([
            'success' => false,
            'error' => "Could not delete the file.",
            'data' => null,
        ]);
    }

    return responseJson([
        'success' => true,
        'data' => null,
    ]);
}

// Copy file
if ($action == 'copy') {
    // Validation
    if (!isset($_POST['from'])) {
        return responseJson([
            'success' => false,
            'error'   => 'Missing `from` key.',
            'data'    => null,
        ]);
    }
    if (empty($_POST['from'])) {
        return responseJson([
            'success' => false,
            'error'   => 'From value is an empty.',
            'data'    => null,
        ]);
    }
    if (!isset($_POST['to'])) {
        return responseJson([
            'success' => false,
            'error'   => 'Missing `to` key.',
            'data'    => null,
        ]);
    }
    if (empty($_POST['to'])) {
        return responseJson([
            'success' => false,
            'error'   => 'To is an empty.',
            'data'    => null,
        ]);
    }
    if (!isset($_POST['file'])) {
        return responseJson([
            'success' => false,
            'error'   => 'Missing `file` key.',
            'data'    => null,
        ]);
    }
    if (empty($_POST['file'])) {
        return responseJson([
            'success' => false,
            'error'   => 'File is an empty.',
            'data'    => null,
        ]);
    }

    $file = trim($_POST['file'], '\\/');
    $from = trim($_POST['from'], '\\/');
    $to   = trim($_POST['to'], '\\/');
    $fromFolder = STORAGE_PATH.DIRECTORY_SEPARATOR.$from;
    $toFolder   = STORAGE_PATH.DIRECTORY_SEPARATOR.$to;
    $fromFilePath = $fromFolder.DIRECTORY_SEPARATOR.$file;
    $toFilePath   = $toFolder.DIRECTORY_SEPARATOR.$file;

    if (!is_dir($fromFolder) && !file_exists($fromFolder)) {
        return responseJson([
            'success' => false,
            'error'   => "Folder [$from] not exists.",
            'data'    => null,
        ]);
    }
    if (!is_dir($toFolder) && !file_exists($toFolder)) {
        return responseJson([
            'success' => false,
            'error'   => "Folder [$to] not exists.",
            'data'    => null,
        ]);
    }
    if (!file_exists($fromFilePath)) {
        return responseJson([
            'success' => false,
            'error'   => "File [$file] not exists.",
            'data'    => null,
        ]);
    }
    // If the file existed in desination folder, append postfix to it
    if (file_exists($toFilePath)) {
        $parts = explode('.', $file);
        $newfile = $parts[0].'_'.getUniqueString().'.'.$parts[1];
        $toNewFilePath = $toFolder.DIRECTORY_SEPARATOR.$newfile;
        if (copy($fromFilePath, $toNewFilePath)) {
            return responseJson([
                'success' => true,
                'data' => getFileInfo($toNewFilePath),
            ]);
        }
        return responseJson([
            'success' => false,
            'error'   => 'Could not copy the file.',
            'data'    => null,
        ]);
    }
    if (copy($fromFilePath, $toFilePath)) {
        return responseJson([
            'success' => true,
            'data'    => getFileInfo($toFolder.DIRECTORY_SEPARATOR.$file),
        ]);
    }
    return responseJson([
        'success' => false,
        'error'   => 'Could not copy the file.',
        'data'    => null
    ]);
}

// Move file
if ($action == 'move') {
    // Validation
    if (!isset($_POST['from'])) {
        return responseJson([
            'success' => false,
            'error'   => 'Missing `from` key.',
            'data'    => null,
        ]);
    }
    if (empty($_POST['from'])) {
        return responseJson([
            'success' => false,
            'error'   => 'From value is an empty.',
            'data'    => null,
        ]);
    }
    if (!isset($_POST['to'])) {
        return responseJson([
            'success' => false,
            'error'   => 'Missing `to` key.',
            'data'    => null,
        ]);
    }
    if (empty($_POST['to'])) {
        return responseJson([
            'success' => false,
            'error' => 'To is an empty.',
            'data' => null,
        ]);
    }
    if (!isset($_POST['file'])) {
        return responseJson([
            'success' => false,
            'error'   => 'Missing `file` key.',
            'data'    => null,
        ]);
    }
    if (empty($_POST['file'])) {
        return responseJson([
            'success' => false,
            'error' => 'File is an empty.',
            'data' => null,
        ]);
    }

    $file = trim($_POST['file'], '\\/');
    $from = trim($_POST['from'], '\\/');
    $to   = trim($_POST['to'], '\\/');
    $fromFolder = STORAGE_PATH.DIRECTORY_SEPARATOR.$from;
    $toFolder   = STORAGE_PATH.DIRECTORY_SEPARATOR.$to;
    $fromFilePath = $fromFolder.DIRECTORY_SEPARATOR.$file;
    $toFilePath   = $toFolder.DIRECTORY_SEPARATOR.$file;

    if (!is_dir($fromFolder) && !file_exists($fromFolder)) {
        return responseJson([
            'success' => false,
            'error'   => "Folder [$from] not exists.",
            'data'    => null,
        ]);
    }
    if (!is_dir($toFolder) && !file_exists($toFolder)) {
        return responseJson([
            'success' => false,
            'error'   => "Folder [$to] not exists.",
            'data'    => null,
        ]);
    }
    if (!file_exists($fromFilePath)) {
        return responseJson([
            'success' => false,
            'error'   => "File [$file] not exists.",
            'data'    => null,
        ]);
    }
    // If the file existed in desination folder, append postfix to it
    if (file_exists($toFilePath)) {
        $parts = explode('.', $file);
        $newfile = $parts[0].'_'.getUniqueString().'.'.$parts[1];
        $fromNewFilePath = $fromFolder.DIRECTORY_SEPARATOR.$newfile;
        if (move_file($fromNewFilePath, $toFolder)) {
            return responseJson([
                'success' => true,
                'data'    => getFileInfo($toFolder.DIRECTORY_SEPARATOR.$toNewFilePath),
            ]);
        }
        return responseJson([
            'success' => false,
            'error'   => 'Could not move the file.',
            'data'    => null,
        ]);
    }
    if (move_file($fromFilePath, $toFolder)) {
        return responseJson([
            'success' => true,
            'data'    => getFileInfo($toFolder.DIRECTORY_SEPARATOR.$file),
        ]);
    }
    return responseJson([
        'success' => false,
        'data'    => 'Could not move the file.'
    ]);
}

return responseJson([
    'success' => false,
    'error' => "Action [{$_POST['action']}] not be allowed.",
    'data' => null,
]);
