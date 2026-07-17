<?php
session_start();
include 'connect.php'; // pastikan fail ini betul


$uploadDir = '../kmz/'; // folder fizikal fail
$publicUrlBase = 'https://jts.sarawak.gov.my/commands/kmz/'; // URL base fail (ubah ikut domain anda)
$add_by = $_SESSION['uid'];
$aq_num = $_POST['sj_id'];
$pj_id = $_POST['pj_id'];
$pages = $_POST['pages'];

if (empty($pages)) {
    $pages = 'default';
} else if ($pages == 'set_ncr') {
    $pages = 'set_ncr';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['files']['name'][0])) {
        foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
            $originalName = basename($_FILES['files']['name'][$index]);
            $targetPath = $uploadDir . $originalName;
            $publicUrl = $publicUrlBase . $originalName;

            // Elak overwrite fail — tukar nama jika sudah ada
            if (file_exists($targetPath)) {
                $uniqueName = time() . '_' . $originalName;
                $targetPath = $uploadDir . $uniqueName;
                $publicUrl = $publicUrlBase . $uniqueName;
                $originalName = $uniqueName;
            }

            if (move_uploaded_file($tmpName, $targetPath)) {
                // Simpan ke DB
                $sql = "INSERT INTO kmz_file (sj_id, kmz_file, file_path, url, add_by)
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssss", $aq_num, $originalName, $targetPath, $publicUrl, $add_by);

                if ($stmt->execute()) {
                    session_start();
                    $_SESSION['status'] = 'success';
                    $_SESSION['message'] = "KMZ upload successfully";
                    if ($pages == 'default') {
                        header("Location: ../project_details.php?project=&pages=lapi&id=" . $pj_id . "&sjid=" . $aq_num);
                    } else if ($pages == 'set_ncr') {
                        header("Location: ../targetSettings.php?id=" . $pj_id . "&sjid=" . $aq_num);
                    }

                    echo "✔ $originalName berjaya dimuat naik & direkod.<br>";
                } else {
                    session_start();
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = "Upload to DB failed";
                    if ($pages == 'default') {
                        header("Location: ../project_details.php?project=&pages=lapi&id=" . $pj_id . "&sjid=" . $aq_num);
                    } else if ($pages == 'set_ncr') {
                        header("Location: ../targetSettings.php?id=" . $pj_id . "&sjid=" . $aq_num);
                    }
                    echo "✖ Gagal simpan $originalName ke DB: " . $stmt->error . "<br>";
                }

                $stmt->close();
            } else {
                session_start();
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Fail to upload file to the server";
                if ($pages == 'default') {
                    header("Location: ../project_details.php?project=&pages=lapi&id=" . $pj_id . "&sjid=" . $aq_num);
                } else if ($pages == 'set_ncr') {
                    header("Location: ../targetSettings.php?id=" . $pj_id . "&sjid=" . $aq_num);
                }
                echo "✖ Gagal muat naik $originalName ke server.<br>";
            }
        }
    } else {
        session_start();
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = "No file chooses";
        if ($pages == 'default') {
            header("Location: ../project_details.php?project=&pages=lapi&id=" . $pj_id . "&sjid=" . $aq_num);
        } else if ($pages == 'set_ncr') {
            header("Location: ../targetSettings.php?id=" . $pj_id . "&sjid=" . $aq_num);
        }
        echo "✖ Tiada fail dipilih.";
    }
} else {
    session_start();
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = "Invalid Request Method";
    if ($pages == 'default') {
        header("Location: ../project_details.php?project=&pages=lapi&id=" . $pj_id . "&sjid=" . $aq_num);
    } else if ($pages == 'set_ncr') {
        header("Location: ../targetSettings.php?id=" . $pj_id . "&sjid=" . $aq_num);
    }
    echo "✖ Invalid request method.";
}

$conn->close();
