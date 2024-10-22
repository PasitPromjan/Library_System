<div class="sidebar bg-dark">
    <!-- Sidebar user panel (optional) -->

    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
                <a href="./index.php?r=dashboard" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>สรุปการยืม</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="./index.php?r=report" class="nav-link">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>รายงาน</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="./index.php?r=mcat_b" class="nav-link">
                    <i class="nav-icon fas fa-tags"></i>
                    <p>หมวดหมู่หนังสือ</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="./index.php?r=m_publisher" class="nav-link">
                    <i class="nav-icon fas fa-print"></i>
                    <p>สำนักงานพิมพ์</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="./index.php?r=book_data" class="nav-link">
                    <i class="nav-icon fas fa-book"></i>
                    <p>ข้อมูลหนังสือ</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="./index.php?r=book_form" class="nav-link">
                    <i class="nav-icon fas fa-edit"></i>
                    <p>เพิ่ม แก้ไข ข้อมูลหนังสือ</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="./index.php?r=borrow_b" class="nav-link">
                    <i class="nav-icon fas fa-arrow-right"></i>
                    <p>ยืมหนังสือ</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="./index.php?r=mborrow_book" class="nav-link">
                    <i class="nav-icon fas fa-history"></i>
                    <p>ข้อมูลการยืมหนังสือ</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="./index.php?r=report_file" class="nav-link">
                    <i class="nav-icon fas fa-folder"></i>
                    <p>ไฟล์รายงาน</p>
                </a>
            </li>
            <?php if ($role == 'admin') { ?>
                <li class="nav-item">
                    <a href="./index.php?r=m_officer" class="nav-link">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>ข้อมูลเจ้าหน้าที่</p>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>

<style>
.sidebar {
    position: fixed; /* ทำให้ sidebar คงที่ */
    height: 100%; /* เต็มความสูง */
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5); /* เงา */
}

.nav-link {
    color: #ffffff; /* สีข้อความ */
    transition: background-color 0.3s, color 0.3s; /* การเปลี่ยนแปลง */
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.2); /* เปลี่ยนพื้นหลังเมื่อ hover */
    color: #ffeb3b; /* เปลี่ยนสีข้อความเมื่อ hover */
}

.nav-icon {
    margin-right: 10px; /* ระยะห่างระหว่างไอคอนกับข้อความ */
}
</style>
