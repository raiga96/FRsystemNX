<?php
include 'connect.php';

if (isset($_POST['save']) || isset($_POST['submit'])) {
    $pj_id = $_POST['pj_id'];
    $sj_id = $_POST['sj_id'];
    $file_names = $_POST['file_name'];
    $files = $_FILES['files'];
    $pages = $_POST['pages'];

    if (empty($pages)) {
        $pages = 'default';
    } else if ($pages == 'set_ncr') {
        $pages = 'set_ncr';
    }

    $target_dir = "../attachments/"; // pastikan folder ini wujud dan boleh ditulis

    // Upload file ikut setiap fail dihantar
    for ($i = 0; $i < count($file_names); $i++) {
        $originalFileName = basename($files['name'][$i]);
        $tmpFilePath = $files['tmp_name'][$i];

        // Check if file was uploaded without error
        if ($files['error'][$i] === 0 && !empty($originalFileName)) {
            $newFileName = time() . '_' . preg_replace("/\s+|[^a-zA-Z0-9.]/", "_", $originalFileName);
            $target_path = $target_dir . $newFileName;
            $file_path = "attachments/" . $newFileName;

            if (move_uploaded_file($tmpFilePath, $target_path)) {
                // Simpan info ke dalam table notice_files
                $stmt2 = $conn->prepare("INSERT INTO attachm (project_id, sj_id, file_name, file_path) VALUES (?, ?, ?, ?)");
                $stmt2->bind_param("ssss", $pj_id, $sj_id, $file_names[$i], $file_path);
                $stmt2->execute();
                $stmt2->close();
            } else {
                //Jika ada error
                session_start();
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Error upload" . count($file_names) . " file!";
                header("Location: ../project_details_formd.php?pages=lapi&id=" . $pj_id . "&sjid=" . $sj_id);
            }
        }
    }


    session_start();
    $_SESSION['status'] = 'success';
    $_SESSION['message'] = count($file_names) . " file uploaded successfully!";
    if ($pages == 'default') {
        header("Location: ../project_details_formd.php?pages=lapi&id=" . $pj_id . "&sjid=" . $sj_id);
    } else if ($pages == 'set_ncr') {
        header("Location: ../targetSettings.php?id=" . $pj_id . "&sjid=" . $sj_id);
    }
    exit();
}
