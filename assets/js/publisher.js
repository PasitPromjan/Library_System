function publisherSubmit() {
    return $('#publisherSubmit')
}

function getPublisherSearch() {
    return $('#publisher-search')
}

$('[name="open-publisher-modal"]').click(function () {
    const act = $(this).attr('data-act')
    const id = $(this).attr('data-id')
    hideErrValidate()
    $('#publisherForm')[0].reset()
    switch (act) {
        case 'insert':
            publisherSubmit().attr('data-id', id).attr('data-act', act)
            $('#publisherModal').modal('show')
            break;
        case 'update':
            publisherSubmit().attr('data-id', id).attr('data-act', act)
            getPublisherDataById(id)
            break;
        default:
            break;
    }

})


function getPublisherDataById(id) {
    $.ajax({
        url: './controller/publisherController.php',
        type: 'post',
        data: {
            'publisher_id': id
        },
        complete: function (xhr, textStatus) {
            try {
                const data = JSON.parse(xhr.responseText)
                if (xhr.status == 200) {
                    const publisherName = data.publisher
                    $('#publisherName').val(publisherName[0].publisher_name)
                    $('#publisherModal').modal('show')
                } else if (xhr.status == 403) {
                    let msg = xhr.responseText
                    let status = xhr.status
                    if (xhr.status == 403) {
                        status = ''
                        msg = 'ไม่สามารถเข้าถึงข้อมูลได้'
                    }
                    errDialog('ข้อผิดพลาด', status, msg)
                }
            } catch (err) {
                errDialog('เกิดข้อผิดพลาด', '', err)
            }
        }
    })
}
$('#publisherSubmit').click(function () {
    const act = publisherSubmit().attr('data-act')
    const id = publisherSubmit().attr('data-id')
    const publisherName = $('#publisherName').val().trim()
    const errAlert = $('#validate-publisherName')
    let isValidate = publisherName == ''

    errValidate(isValidate, errAlert, 'กรุณาป้อนสำนักพิมพ์')
    if (!isValidate) {
        $.ajax({
            url: './controller/publisherController.php',
            type: 'post',
            data: {
                'act': act,
                'publisher_name': publisherName,
            },
            complete: function (xhr, textStatus) {
                try {
                    const data = JSON.parse(xhr.responseText)
                    let msg = data.isValidate
                    let status = xhr.status
                    if (xhr.status == 200) {
                        success('บันทึกข้อมูลเรียบร้อย')
                    } else {
                        if (isValidate == false) {
                            msg = 'มีหมวดนี้อยู่ในระบบแล้ว'
                            status = ''
                        }
                        errDialog('แจ้งเตือน', status, msg)
                    }
                } catch (err) {
                    console.error(err)
                    errDialog('เกิดข้อผิดพลาด', '', err)
                }
            }
        })
    }

})


$('button[name="publisher-remove"]').click(function () {
    const id = $(this).attr('data-id')
    confirmDialog('ลบข้อมูลสำนักพิมพ์', 'คุณต้องการลบข้อมูลรายการนี้ใช่ หรือไม่ ?')
        .then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: './controller/publisherController.php',
                    type: 'post',
                    data: {
                        'publisher_id': id,
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




$('#publisher-submit').click(function () {
    const v = getPublisherSearch().val().trim().replaceAll(' ', '-')
    if (v != '') {
        window.location.assign(`./index.php?r=m_publisher&n=${v}`)
    }
})


getPublisherSearch().keyup(function (e) {
    const v = $(this).val().trim()
    const keyCode = e.keyCode
    if (keyCode == 13) {
        if (v != '') {
            window.location.assign(`./?r=m_publisher&n=${v}`)
        }
    }

})