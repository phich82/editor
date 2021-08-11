<?php

if (isset($_FILES['upload']['name'])) {
    $file = $_FILES['upload']['tmp_name'];
    $file_name = $_FILES['upload']['name'];
    $file_name_array = explode(".", $file_name);
    $extension = end($file_name_array);
    //we want to save the image with timestamp and randomnumber
    $new_image_name = time() . rand(). '.' . $extension;
    $pathUpload = __DIR__ . '/uploads';
    chmod($pathUpload, 0777);
    $allowed_extension = ['jpg', 'jpeg', 'gif', 'png'];

    if (in_array($extension, $allowed_extension)) {
        $uploaded = move_uploaded_file($file, $pathUpload.'/' . $new_image_name);
        $function_number = $_GET['CKEditorFuncNum'] ?? '';
        $url = 'ckeditor/uploader/uploads/' . $new_image_name;
        $message = 'Uploaded successfully.';
        if (!$uploaded) {
            $message = 'Uploaded failed.';
            $url = '';
        }
        // Return file url for ckeditor
        echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($function_number, '$url', '$message');</script>";
    }
}