<?php
require_once __DIR__ . '/../src/bootstrap.php';

use CT275\Labs\Contact;

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    ## Gọi hàm xử lý ảnh upload
    $avatar_file = handle_avatar_upload();



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
        $contact = new Contact($PDO);
        $contact->fill($_POST);
        if ($contact->validate()) {

            ## Có cái này mới có ảnh
            $contact->avatar = $avatar_file;


            $contact->save() && redirect('/');
        }
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
        $subtitle = 'Add your contacts here.';
        include_once __DIR__ . '/../src/partials/heading.php';
        ?>

        <div class="row">
            <div class="col-12">

                <form method="post" class="col-md-6 offset-md-3" enctype="multipart/form-data">

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" maxlen="255" id="name" placeholder="Enter Name" value="<?= isset($_POST['name']) ? html_escape($_POST['name']) : '' ?>" />

                        <?php if (isset($errors['name'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['name'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone" class="form-control<?= isset($errors['phone']) ? ' is-invalid' : '' ?>" maxlen="255" id="phone" placeholder="Enter Phone" value="<?= isset($_POST['phone']) ? html_escape($_POST['phone']) : '' ?>" />

                        <?php if (isset($errors['phone'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['phone'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>

                    <!-- Notes -->
                    <div class="form-group">
                        <label for="notes">Notes </label>
                        <textarea name="notes" id="notes" class="form-control<?= isset($errors['notes']) ? ' is-invalid' : '' ?>" placeholder="Enter notes (maximum character limit: 255)"><?= isset($_POST['notes']) ? html_escape($_POST['notes']) : '' ?></textarea>

                        <?php if (isset($errors['notes'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['notes'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>

                    <!-- Avatar Preview -->
                    <div class="form-group">
                        <label for="avatar" id="avatar-preview1" style="display: none;">Avatar Preview:</label>
                        <img id="avatar-preview" src="#" alt="Avatar" style="display: none; width: 100px; height: 100px; border-radius: 50%;">
                    </div>

                    <!-- Avatar Input -->
                    <div class="form-group">
                        <label for="avatar">Choose avatar:</label>
                        <input type="file" name="avatar" id="avatar-input">
                    </div>

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


                    <!-- Submit -->
                    <button type="submit" name="submit" class="btn btn-primary">Add Contact</button>
                </form>

            </div>
        </div>

    </div>

    <?php include_once __DIR__ . '/../src/partials/footer.php' ?>
</body>

</html>