<?php
require_once __DIR__ . '/../src/bootstrap.php';

use CT275\Labs\Contact;

## Gọi hàm xử lý ảnh

$avatar_file = handle_avatar_upload();


$contact = new Contact($PDO);

$id = isset($_REQUEST['id']) ?
    filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT) : -1;
if ($id < 0 || !($contact->find($id))) {
    redirect('/');
}
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    ## avatar_file không phải là false và không phải là null thì nó sẽ là tên tập tin được upload lên
    ## do đó nhận biết người dùng đã uplaod ảnh
    if ($avatar_file !== false && $avatar_file !== null) {
        // Kiểm tra kích thước tập tin, không được vượt quá 8KB
        if ($_FILES['avatar']['size'] > 8 * 1024) {
            $errors['avatar'] = "Lỗi vượt quá kích thước ảnh";
            echo "<script>alert('Vui lòng chọn một file ảnh có kích thước nhỏ hơn 8KB.');</script>";
        }
    }



    if (empty($errors)) {
        ## Nếu người dùng chọn file ảnh mới để thay thế file ảnh cũ
        if ($avatar_file && !empty($_POST['old_avatar'])) {
            $_POST['avatar'] = $avatar_file;
            remove_avatar_file($_POST['old_avatar']);
        }

        ## Nếu người dùng chọn avatar mới khi contact đã được tạo và chưa có avatar 
        if ($avatar_file && empty($_POST['old_avatar'])) {
            $_POST['avatar'] = $avatar_file;
        }

        ## Nếu người dùng không chọn avatar mới nào 
        if (!$avatar_file) {
            $_POST['avatar'] = $_POST['old_avatar'];
        }

        if ($contact->update($_POST)) {
            // Cập nhật dữ liệu thành công
            redirect('/');
        }
        // Cập nhật dữ liệu không thành công
        $errors = $contact->getValidationErrors();
    }
}

include_once __DIR__ . '/../src/partials/header.php';
?>

<body>
    <?php include_once __DIR__ . '/../src/partials/navbar.php' ?>

    <!-- Main Page Content -->
    <div class="container">

        <?php
        $subtitle = 'Update your contacts here.';
        include_once __DIR__ . '/../src/partials/heading.php';
        ?>

        <div class="row">
            <div class="col-12">

                <form method="post" class="col-md-6 offset-md-3" enctype="multipart/form-data">

                    <input type="hidden" name="id" value="<?= $contact->getId() ?>">

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" maxlen="255" id="name" placeholder="Enter Name" value="<?= html_escape($contact->name) ?>" />

                        <?php if (isset($errors['name'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['name'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone" class="form-control<?= isset($errors['phone']) ? ' is-invalid' : '' ?>" maxlen="255" id="phone" placeholder="Enter Phone" value="<?= html_escape($contact->phone) ?>" />

                        <?php if (isset($errors['phone'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['phone'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>

                    <!-- Notes -->
                    <div class="form-group">
                        <label for="notes">Notes </label>
                        <textarea name="notes" id="notes" class="form-control<?= isset($errors['notes']) ? ' is-invalid' : '' ?>" placeholder="Enter notes (maximum character limit: 255)"><?= html_escape($contact->notes) ?></textarea>

                        <?php if (isset($errors['notes'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['notes'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>

                    <!-- Avatar -->
                    <div class="form-group">

                        <div>
                            <label for="avatar">Old avatar: </label>
                            <!-- Hiện avatar cũ -->
                            <img src="<?= '/uploads/' . html_escape($contact->avatar) ?>" alt="avatar" style="width: 100px; height: 100px; border-radius: 50%;">
                            <input type="hidden" name="old_avatar" value="<?= html_escape($contact->avatar) ?>">
                        </div>

                        <!-- Avatar Preview -->
                        <div class="form-group">
                            <label for="avatar" id="avatar-preview1" style="display: none;">Avatar Preview:</label>
                            <img id="avatar-preview" src="#" alt="Avatar" style="display: none; width: 100px; height: 100px; border-radius: 50%;">
                        </div>

                        <!-- Avatar Input -->
                        <div class="form-group">
                            <label for="avatar">Choose new Avatar:</label>
                            <input type="file" name="avatar" id="avatar-input">
                        </div>

                        <!-- jQuery để review ảnh -->
                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                        <script>
                            $(document).ready(function() {
                                $('#avatar-input').change(function() {
                                    var input = this;
                                    if (input.files && input.files[0]) {
                                        var reader = new FileReader();
                                        reader.onload = function(e) {
                                            $('#avatar-preview').attr('src', e.target.result).show();
                                            $('#avatar-preview1').show();
                                        }
                                        reader.readAsDataURL(input.files[0]);
                                    }
                                });
                            });
                        </script>

                        <?php if (isset($errors['avatar'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['avatar'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>


                    <!-- Submit -->
                    <button type="submit" name="submit" class="btn btn-primary">Update Contact</button>
                </form>

            </div>
        </div>

    </div>

    <?php include_once __DIR__ . '/../src/partials/footer.php' ?>
</body>

</html>