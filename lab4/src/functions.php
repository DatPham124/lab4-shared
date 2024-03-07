<?php

function redirect(string $location): void
{
    header('Location: ' . $location, true, 302);
    exit();
}

function html_escape(string|null $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8', false);
}

 ## Hàm xử lý upload ảnh
 function handle_avatar_upload(): string | bool
 {
     if (!isset($_FILES['avatar'])) {
         return false;
     }
 
     $avatar = $_FILES['avatar'];
     $avatar_name = $avatar['name'];
     $avatar_tmp_name = $avatar['tmp_name'];
     $avatar_size = $avatar['size'];
     $avatar_error = $avatar['error'];
     
     $avatar_new_name = uniqid() . '_' . $avatar_name;
     $avatar_destination = __DIR__ . '/../public/uploads/' . $avatar_new_name;
 
     if (!move_uploaded_file($avatar_tmp_name, $avatar_destination)) {
         return false;
     }
 
     return $avatar_new_name;
 }
 

 ## Hàm xóa file ảnh trong thư mục uploads
function remove_avatar_file(string $filename): bool
{
    $file_path = __DIR__ . '/../public/uploads/' . $filename;
    if (!file_exists($file_path)) {
        return false;
    }

    return unlink($file_path);
}
