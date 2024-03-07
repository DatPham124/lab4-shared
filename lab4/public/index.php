<?php
require_once __DIR__ . '/../src/bootstrap.php';

use CT275\Labs\Contact;
use CT275\Labs\Paginator;

$contact = new Contact($PDO);
$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ?
    (int)$_GET['limit'] : 5;
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ?
    (int)$_GET['page'] : 1;
$paginator = new Paginator(
    totalRecords: $contact->count(),
    recordsPerPage: $limit,
    currentPage: $page
);
$contacts = $contact->paginate($paginator->recordOffset, $paginator->recordsPerPage);
$pages = $paginator->getPages(length: 3);
include_once __DIR__ . '/../src/partials/header.php';
?>

<body>
    <?php include_once __DIR__ . '/../src/partials/navbar.php' ?>

    <!-- Main Page Content -->
    <div class="container">

        <?php
        $subtitle = 'View your all contacs here.';
        include_once __DIR__ . '/../src/partials/heading.php';
        ?>

        <div class="row">
            <div class="col-12">

                <a href="/add.php" class="btn btn-primary mb-3">
                    <i class="fa fa-plus"></i> New Contact
                </a>

                <!-- Table Starts Here -->
                <table id="contacts" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <!-- Thêm cột avatar vào trang chủ-->
                            <th scope="col">Avatar</th>

                            <th scope="col">Name</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Date Created</th>
                            <th scope="col">Notes</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact) : ?>
                            <tr>
                                <!-- Thêm ảnh vào cột avatar trong trang chủ và định dạng avatar trong thẻ style -->
                                <td><img src="<?= '/uploads/' . html_escape($contact->avatar) ?>" alt="avatar" style="width: 100px; height: 100px; border-radius: 50%;"></td>
                                <td><?= html_escape($contact->name) ?></td>
                                <td><?= html_escape($contact->phone) ?></td>
                                <td><?= html_escape(date("d-m-Y", strtotime($contact->created_at))) ?>

                                </td>

                                <td><?= html_escape($contact->notes) ?></td>
                                <td class="d-flex justify-content-center">
                                    <a href="<?= '/edit.php?id=' . $contact->getId() ?>" class="btn btn-xs btn-warning">
                                        <i alt="Edit" class="fa fa-pencil"></i> Edit</a>
                                    <form class="form-inline ml-1" action="/delete.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $contact->getId() ?>">
                                        <button type="submit" class="btn btn-xs btn-danger" name="delete-contact">
                                            <i alt="Delete" class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <!-- Table Ends Here -->

                <!-- Pagination -->
                <nav class="d-flex justify-content-center">
                    <ul class="pagination">
                        <li class="page-item<?= $paginator->getPrevPage() ?
                                                '' : ' disabled' ?>">
                            <a role="button" href="/?page=<?= $paginator->getPrevPage() ?>&limit=5" class="page-link">
                                <span>&laquo;</span>
                            </a>
                        </li>
                        <?php foreach ($pages as $page) : ?>
                            <li class="page-item<?= $paginator->currentPage === $page ?
                                                    ' active' : '' ?>">
                                <a role="button" href="/?page=<?= $page ?>&limit=5" class="page-link"><?= $page ?></a>
                            </li>
                        <?php endforeach ?>
                        <li class="page-item<?= $paginator->getNextPage() ?
                                                '' : ' disabled' ?>">
                            <a role="button" href="/?page=<?= $paginator->getNextPage() ?>&limit=5" class="page-link">
                                <span>&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div id="delete-confirm" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirmation</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">Do you want to delete this contact?</div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-danger" id="delete">Delete</button>
                    <button type="button" data-dismiss="modal" class="btn btn-default">Cancel</button>
                </div>
            </div>
        </div>
    </div>



    <?php include_once __DIR__ . '/../src/partials/footer.php' ?>
    <script>
        $(document).ready(function() {
            $('button[name="delete-contact"]').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const nameTd = $(this).closest('tr').find('td:first');
                if (nameTd.length > 0) {
                    $('.modal-body').html(
                        `Do you want to delete "${nameTd.text()}"?`
                    );
                }
                $('#delete-confirm').modal({
                        backdrop: 'static',
                        keyboard: false
                    })
                    .on('click', '#delete', function() {
                        form.trigger('submit');
                    });
            });
        });
    </script>
</body>

</html>