function officerHandleSubmit() {
    return $('#officerHandleSubmit')
}
$('button[name="officer-remove"]').click(function () {
    const id = $(this).attr('data-id')
    confirmDialog('ลบข้อมูลเจ้าหน้าที่', 'คุณต้องการลบข้อมูลรายการนี้ใช่ หรือไม่ ?')
        .then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: './controller/officerController.php',
                    type: 'post',
                    data: {
                        'id': id,
                        'act': 'delete'
                    },
                    complete: function (xhr, textStatus) {
                        try {
                            if (xhr.status == 200) {
                                success('ลบข้อมูลสำเร็จ')
                            } else {
                                errDialog('แจ้งเตือน', xhr.status, xhr.responseText)
                            }
                        } catch (err) {
                            console.error(err)
                            errDialog('เกิดข้อผิดพลาด', '', err)
                        }
                    }
                })
            }
        });

})
$('button[name="open-officer-modal"]').click(function () {
    const act = $(this).attr('data-act')
    const id = $(this).attr('data-id')
    const isUsername = act == 'update'
    $('#username').prop('disabled', isUsername)
    $('#officerForm')[0].reset()
    hideErrValidate()
    switch (act) {
        case 'insert':
            officerHandleSubmit().attr('data-act', act)
            $('#officerModal').modal('show');
            break;
        case 'update':
            officerHandleSubmit().attr('data-act', act).attr('data-id', id)
            getOfficerDataById(id)
            break;
        default:
            break;
    }

})

function getOfficerDataById(id) {
    console.log(id)
    $.ajax({
        url: './controller/officerController.php',
        type: 'post',
        data: {
            'id': id
        },
        complete: function (xhr, textStatus) {
            try {
                const data = JSON.parse(xhr.responseText)
                const officerData = data.officer
                if (xhr.status == 200) {
                    $('#officer-fname').val(officerData.officer_fname)
                    $('#officer-lname').val(officerData.officer_lname)
                    $('#username').val(officerData.username)
                    retainRadio(officerData.role, $('[name="officer-role"]'))
                    $('#officerModal').modal('show');
                } else {
                    errDialog('แจ้งเตือน', xhr.status, xhr.responseText)
                }
            } catch (err) {
                errDialog('เกิดข้อผิดพลาด', '', err)
            }

        }
    })

}


officerHandleSubmit().click(function () {
    const officerForm = [{
        'formtype': 'text',
        'input': $('#username'),
        'alert': $('#validate-username'),
        'msg': 'กรุณาป้อนชื่อบัญชีผู้ใช้งาน'
    }, {
        'formtype': 'text',
        'name': 'password',
        'input': $('#password'),
        'alert': $('#validate-password'),
        'msg': 'กรุณาป้อนรหัสผ่าน'
    }, {
        'formtype': 'text',
        'input': $('#officer-fname'),
        'alert': $('#validate-fname'),
        'msg': 'กรุณาป้อนชื่อเจ้าหน้าที่'
    }, {
        'formtype': 'text',
        'input': $('#officer-lname'),
        'alert': $('#validate-lname'),
        'msg': 'กรุณาป้อนนามสกุล'
    }, {
        'formtype': 'radio',
        'input': $('[name="officer-role"]'),
        'alert': $('#validate-role'),
        'msg': 'กรุณาเลือกบทบาท'
    }]

    const isChangePassword = $('#changePassword').is(':checked')
    let emptyCount = 0
    officerForm.forEach((fd) => {
        const {
            formtype,
            msg,
            alert,
            input
        } = fd
        let isValidate = false
        if (formtype == 'text' && input.val().trim() == '') {
            if (isChangePassword && fd.name == 'password') {
                emptyCount++
                isValidate = true
            }
            if (fd.name != 'password') {
                emptyCount++
                isValidate = true
            }

        }

        if (formtype == 'radio' && input.filter(':checked').length == 0) {
            emptyCount++
            isValidate = true
        }
        errValidate(isValidate, alert, msg)
    })

    if (emptyCount == 0) {
        const act = officerHandleSubmit().attr('data-act')
        const id = officerHandleSubmit().attr('data-id')
        const formData = new FormData()
        formData.append('act', act)
        formData.append('username', $('#username').val())

        formData.append('officer_fname', $('#officer-fname').val())
        formData.append('officer_lname', $('#officer-lname').val())
        formData.append('role', $('[name="officer-role"]').filter(':checked').val())
        formData.append('password', $('#password').val())

        if (act == 'update') {
            formData.append('id', id)
        }
        $.ajax({
            url: './controller/officerController.php',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            complete: function (xhr, textStatus) {
                try {
                    const data = JSON.parse(xhr.responseText)
                    const isUsername = data.isUsername
                    if (xhr.status == 200) {
                        success('บันทึกข้อมูลเรียบร้อย')
                    } else {
                        let text = xhr.responseText
                        let status = xhr.status
                        if (isUsername) {
                            text = 'มีชื่อบัญชีผู้ใช้นี้อยู่ในระบบแล้ว โปรดป้อนชื่อบัญชีผู้ใช้งานใหม่'
                            status = ''
                        }
                        errDialog('แจ้งเตือน', status, text)
                    }
                } catch (err) {
                    errDialog('เกิดข้อผิดพลาด', '', err)
                }

            }
        })
    }
})