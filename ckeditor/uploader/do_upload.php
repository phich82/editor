<?php

if ($_FILES['file']['name'] != '') {
    $filename = explode('.', $_FILES['file']['name']);
    $extension = end($filename);
    $filename = time() . rand() . '.' . $extension;

    $location = 'uploads/'.$filename;
    $uploaded = move_uploaded_file($_FILES['file']['tmp_name'], $location);

    $imagePath = 'http://'.$_SERVER['HTTP_HOST'].'/ckeditor/uploader/uploads/'.$filename;

    if (!$uploaded) {
        $imagePath = '';
    }

    echo $imagePath;
}
