<?php
include_once './functions.php';

if ($_FILES['file']['name'] != '') {
    $filename = explode('.', $_FILES['file']['name']);
    $extension = end($filename);
    $filename = time() . rand() . '.' . $extension;
    $defaultFolder = 'storage/Images';

    if (isset($_POST['folder']) && !empty($_POST['folder'])) {
        $defaultFolder = 'storage/'.trim($_POST['folder'], '/\\');
    }

    $location = $defaultFolder.'/'.$filename;

    $uploaded = move_uploaded_file($_FILES['file']['tmp_name'], $location);

    $success = true;
    $errorMessage = '';
    $imageSrc = 'http://'.$_SERVER['HTTP_HOST'].'/ckeditor/uploader/'.$defaultFolder.'/'.$filename;

    if (!$uploaded) {
        $success = false;
        $imageSrc = '';
        $errorMessage = 'Could not upload.';
    }

    return responseJson(array_merge(
        [
            'success' => $success,
            'data' => $imageSrc,
        ],
        !$success
            ? ['error' => $errorMessage]
            : []
    ));
}

return responseJson(array_merge(
    [
        'success' => false,
        'data'    => null,
        'error'   => 'File not exists.'
    ],
));
