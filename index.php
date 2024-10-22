<?php
@session_start();
require_once('./config_menu.php');
require_once('./function/function.php');
require_once('./pag.php');
if (!isset($_SESSION['officer_id'])) {
    header('location:./signin.php');
}

$r = $_GET['r'] ?? 'dashboard';
$template = $menu[$r]['template'];
$title = $menu[$r]['title'] ?? '';

?>

<?php

    $officer_id = $_SESSION['officer_id'] ?? '';
    $username = $_SESSION['username'] ?? '';
    $fname = $_SESSION['name'] ?? '';
    $role = $_SESSION['officer_role'] ?? '';
    $role_name =  get_roleName($role) ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title  ?></title>
    <?php require_once('./header.php')  ?>
</head>



<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ml-5">
                <li><div class="text-dark  m-0"><?php echo $username  ?></div></li>
            </ul>
            <ul class="navbar-nav ml-2"><li><div class="m-0 text-muted"><?php echo $fname  ?></div></li></ul>
            <ul class="navbar-nav ml-5"><li><h5><span class="m-0 badge badge-danger">
                        <?php echo $role_name  ?>
                    </span></h5>
                </li></ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <button id="signOutHandleSubmit" class="nav-link">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>ออกจากระบบ</span>
                    </button>
                </li>
            </ul>
        </nav>
        <aside class="main-sidebar sidebar-dark-red elevation-3">
            <a href="./index.php?r=dashboard" class="brand-link">
                <img src="" class="brand-image" style="opacity: .8">
                <span class="brand-text font-weight-bold text-light">
                    <span>LIBRARY</span>
                    <p class="brand-text font-weight-bold text-danger">ระบบห้องสมุด</p>
                </span>

            </a>
            <?php require_once('./sidebar.php')  ?>
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <section class="content">
                <div class="container py-1">
                    <?php require_once("part/$template.php") ?>
                </div>
            </section>
            <script>
                const params = new URLSearchParams(location.search)
                const p = params.get('r') ?? ''
                $.each($('.nav-link'), (index, navLink) => {
                    const pathname = location.pathname
                    const protocol = location.protocol
                    const host = location.host
                    const href = $(navLink).attr('href')
                    const url = new URL(`${protocol}//${host}${pathname}${href}`)
                    const _p = url.searchParams
                    const link = _p.get('r')


                    if (link == p) $(navLink).addClass('active')
                    const navItem = $(navLink).parent().parent().parent().attr('id')
                    if (navItem) {
                        const nav_treeview = $(`#${navItem}`)
                            .children(':eq(1)')
                            .children()
                            .children()
                        const nav_treeview_items = $.map(nav_treeview, (el) => $(el).attr('href')).join(' ')
                        $.each($(nav_treeview), (i, el) => {
                            if ($(el).hasClass('active')) {
                                $(`#${navItem}`).addClass('menu-is-open')
                                $(`#${navItem}`).addClass('menu-open')
                            }
                        })
                    }
                })

                $('#signOutHandleSubmit').click(function() {

                    $.ajax({
                        url: './signout.php',
                        type: 'post',
                        complete: function(xhr, textStatus) {
                            try {
                                const data = JSON.parse(xhr.responseText)
                                if (xhr.status == 200) {
                                    success('ออกจากระบบเรียบร้อย')
                                } else {
                                    errDialog('เกิดข้อผิดพลาด', 'ไม่สามารถออกจากระบบ', '')
                                }
                            } catch (err) {
                                errDialog('เกิดข้อผิดพลาด', '', err)
                            }
                        }
                    })

                })
            </script>
        </div>
    </div>

    <link rel="stylesheet" href="./assets/AdminLTE-3.2.0/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <script src="./assets/AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
    <script src="./assets/AdminLTE-3.2.0/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="./assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/AdminLTE-3.2.0/plugins/sparklines/sparkline.js"></script>
    <script src="./assets/AdminLTE-3.2.0/plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="./assets/AdminLTE-3.2.0/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <script src="./assets/AdminLTE-3.2.0/plugins/sparklines/sparkline.js"></script>
    <script src="./assets/AdminLTE-3.2.0/plugins/moment/moment.min.js"></script>
    <script src="./assets/AdminLTE-3.2.0/plugins/daterangepicker/daterangepicker.js"></script>

    <script src="./assets/AdminLTE-3.2.0/plugins/summernote/summernote-bs4.min.js"></script>
    <script src="./assets/AdminLTE-3.2.0/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <script src="./assets/AdminLTE-3.2.0/dist/js/adminlte.js"></script>
</body>

</html>



</html>