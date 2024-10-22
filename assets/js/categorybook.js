function categorySubmit() {
    return $('#categorySubmit')
}

$('[name="open-cat-modal"]').click(function () {
    const act = $(this).attr('data-act')
    const id = $(this).attr('data-id')
    hideErrValidate()
    $('#categoryBook').val('')
    switch (act) {
        case 'insert':
            categorySubmit()
                .attr('data-act', 'insert')
                .attr('data-id', '')
            $('#categoryBookModal').modal('show')
            break;
        case 'update':
            categorySubmit()
                .attr('data-act', 'update')
                .attr('data-id', id)
            getCategoryDataById(id)
            break;
        default:
            break;
    }

})



function getCategoryDataById(id) {
    $.ajax({
        url: './controller/categoryController.php',
        type: 'post',
        data: {
            'id': id,
        },
        complete: function (xhr, textStatus) {
            try {
                const data = JSON.parse(xhr.responseText)
                const isValidate = data.isValidate
                if (xhr.status == 200) {
                    const categoryData = data.category
                    $('#categoryBook').val(categoryData[0].category_name)
                    $('#categoryBookModal').modal('show')
                } else {
                    errDialog('แจ้งเตือน', xhr.status, xhr.responseText)
                }
            } catch (err) {
                errDialog('เกิดข้อผิดพลาด', '', err)
            }

        }
    })
}
$('#categorySubmit').click(function () {
    const act = categorySubmit().attr('data-act')
    const id = categorySubmit().attr('data-id')
    const category = $('#categoryBook')
    const alertErr = $('#validate-categoryBook')
    let isValidate = false
    if (category.val().trim() == '') isValidate = true
    errValidate(isValidate, alertErr, 'กรุณาป้อนหมวดหมู่หนังสือ')
    if (isValidate == false) {
        $.ajax({
            url: './controller/categoryController.php',
            type: 'post',
            data: {
                'act': act,
                'id': id,
                'category': $('#categoryBook').val()
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

$('button[name="category-remove"]').click(function () {
    const id = $(this).attr('data-id')
    confirmDialog('ลบข้อมูลหมวดหมู่หนังสือ', 'คุณต้องการลบข้อมูลรายการนี้ใช่ หรือไม่ ?')
        .then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: './controller/categoryController.php',
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

function getFindByName() {
    return $('#findByName')
}
$('#findByNameBtn').click(function () {
    const name = getFindByName().val().trim().replaceAll(' ', '-')
    if (name != '') {
        location.assign(`./?r=mcat_b&n=${name}`)
    }
})
getFindByName().keyup(function (e) {
    const name = getFindByName().val().trim().replaceAll(' ', '-')
    if (e.keyCode == 13 && name != '') {
        location.assign(`./?r=mcat_b&n=${name}`)
    }
})