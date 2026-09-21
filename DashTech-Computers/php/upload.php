<?php

$uploadedFile = null;

if (!empty($_FILES['attachment']['name'])) {

    $allowed = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'pdf',
        'doc',
        'docx'
    ];

    $file = $_FILES['attachment'];

    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        die('Unable to upload file.');
    }

    $extension = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    if (!in_array($extension, $allowed)) {

        die("Invalid file type.");

    }

    if ($file['size'] > 5 * 1024 * 1024) {

        die("Maximum upload size is 5MB.");

    }

    $allowedMimeTypes = [
        'image/jpeg', 'image/png', 'image/webp', 'application/pdf', 'application/zip',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!in_array($mime, $allowedMimeTypes, true)) {
        die('Invalid file content.');
    }

    $newName = bin2hex(random_bytes(16)) . "." . $extension;

    $destination = __DIR__ . "/../uploads/" . $newName;

    if (!move_uploaded_file(
        $file['tmp_name'],
        $destination
    )) {

        die("Unable to upload file.");

    }

    $uploadedFile = 'uploads/' . $newName;

}
