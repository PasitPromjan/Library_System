<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงชื่อเข้าสู่ระบบ</title>
    <script src="./assets/jquery/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="./assets/bootstrap-4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="./assets/fontawesome-free-6.5.1-web/css/all.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <script src="./assets/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./assets/js/sweetalert.js"></script>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            width: 100vw;
            padding: 0;
            margin: 0;
            background-image: url('./assets/img/wrapperbg.jpg');
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-4">
            <div class="card shadow-lg rounded-3 border-0">
                <div class="card-body p-4">
                    <h4 class="text-center mb-4">ลงชื่อเข้าสู่ระบบ</h4>
                    <div class="form-group mb-3">
                        <label for="admin" class="form-label">บัญชีผู้ดูแล</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-white">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <input type="text" class="form-control" id="admin" placeholder="ป้อนชื่อเจ้าหน้าที่">
                        </div>
                        <p class="err-validate text-danger small mt-1" id="empty-admin"></p>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">รหัสผ่าน</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-white">
                                <i class="fa-solid fa-key"></i>
                            </span>
                            <input type="password" class="form-control" id="password" placeholder="ป้อนรหัสผ่าน">
                            <button type="button" onclick="obscureText('#password')" class="btn btn-outline-secondary">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <p class="err-validate text-danger small mt-1" id="empty-password"></p>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-dark btn-lg" id="login">ลงชื่อเข้างาน</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    <script>
        const login = $('#login');

        const loginForm = [{
            'name': 'admin',
            'input': $('#admin'),
            'alert': $('#empty-admin'),
            'msg': 'กรุณาป้อนชื่อเจ้าหน้าที่'
        }, {
            'name': 'password',
            'input': $('#password'),
            'alert': $('#empty-password'),
            'msg': 'กรุณาป้อนรหัสผ่าน'
        }];

        login.click(function () {
    let emptyCount = 0;
    loginForm.forEach((e) => {
        const v = e.input.val().trim();
        const msg = e.msg;
        if (v == '') {
            e.alert.css('display', 'block');
            e.alert.text(msg);
            emptyCount++;
        } else {
            e.alert.css('display', 'none');
            e.alert.text('');
        }
    });
    
    if (emptyCount == 0) {
        const data = {
            'admin': loginForm[0].input.val().trim(),
            'password': loginForm[1].input.val().trim()
        };
        
        $.ajax({
            url: './signin_dp.php',
            type: 'post',
            data: data,
            dataType: 'json',
            complete: function (xhr, textStatus) {
                let msg = '';
                let is_validate = true;

                try {
                    const data = JSON.parse(xhr.responseText);

                    if (data.result) {
                        location.assign('./');
                    } else {
                        is_validate = false;
                        if (data.is_username === false) {
                            msg = data.message || 'ไม่พบผู้ใช้บัญชีนี้ในระบบ';
                        } else if (data.is_password === false) {
                            msg = data.message || 'รหัสผ่านไม่ถูกต้อง';
                        } else {
                            msg = data.err || 'เกิดข้อผิดพลาด';
                        }
                    }

                    if (!is_validate) {
                        errDialog('แจ้งเตือน', msg, '');
                    }

                } catch (err) {
                    errDialog('ข้อผิดพลาด', 'มีปัญหาในการประมวลผล', err);
                }
            }
        });
    }
});

    </script>

    <script src="assets/js/function.js"></script>
</body>

</html>
