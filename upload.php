<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "file_upload";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileType = $_FILES['file']['type'];

    $id = 1;
    $randomFileName = 'PDF_File.pdf';
    $uploadDirectory = 'file_pdf/' . $randomFileName;

    $checkSql = "SELECT * FROM files WHERE id_files='$id'";
    $result = $conn->query($checkSql);

    if ($result->num_rows > 0) {
        // File exists, update the record and replace the file
        if (move_uploaded_file($fileTmpName, $uploadDirectory)) {
            $updateSql = "UPDATE files SET nama_file='$fileName' WHERE file_pdf='$randomFileName'";

            if ($conn->query($updateSql) === TRUE) {
                $status = 'success';
                $message = 'File berhasil diunggah dan diperbarui.';
            } else {
                $status = 'error';
                $message = 'Error: ' . $updateSql . '<br>' . $conn->error;
            }
        } else {
            $status = 'error';
            $message = 'Error saat memindahkan file yang diunggah.';
        }
    } else {
        // File does not exist, insert a new record
        if (move_uploaded_file($fileTmpName, $uploadDirectory)) {
            $insertSql = "INSERT INTO files (nama_file, file_pdf) VALUES ('$fileName', '$randomFileName')";

            if ($conn->query($insertSql) === TRUE) {
                $status = 'success';
                $message = 'File berhasil diunggah dan disimpan.';
            } else {
                $status = 'error';
                $message = 'Error: ' . $insertSql . '<br>' . $conn->error;
            }
        } else {
            $status = 'error';
            $message = 'Error saat memindahkan file yang diunggah.';
        }
    }
} else {
    $status = 'error';
    $message = 'Tidak ada file yang diunggah atau terjadi kesalahan saat mengunggah.';
}

// Menutup koneksi
$conn->close();

header("Location: index.html?status=$status&message=" . urlencode($message));
exit();
