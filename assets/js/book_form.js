createSelect2Remote("#bookCategory", './controller/categoryController.php', 'ไม่พบหมวดของหนังสือที่ค้นหา', 'ค้นหาหมวดหมู่หนังสือ')
createSelect2Remote("#publisherName", './controller/publisherController.php', 'ไม่พบสำนักพิมพ์ที่ต้องการ', 'ค้นหาสำนักพิมพ์')

document.addEventListener('DOMContentLoaded', () => {
    console.log($('#bookCategoryRetain').val())
    retainOption($('#bookCategoryRetain').val(), $('#bookCategory'))
    retainOption($('#publisherNameRetain').val(), $('#publisherName'))
})



function bookHandleSubmit() {
    return $('#bookHandleSubmit')
}




$('#bookImg').change(function () {
    const f = $(this)[0].files[0]
    const bookImagePreview = $('#bookImagePreview')
    if (f != undefined) {
        const src = URL.createObjectURL(f)
        bookImagePreview.html(`<img src="${src}">`)
    } else if (f == undefined) {
        bookImagePreview.html('')
    }
})

bookHandleSubmit().click(function () {
    const act = bookHandleSubmit().attr('data-act')
    const id = bookHandleSubmit().attr('data-id')
    const bookForm = [{
        'formtype': 'text',
        'input': $('#bookname'),
        'msg': 'ป้อนชื่อหนังก่อน',
        'alert': $('#validate-bookname')
    }, {
        'formtype': 'text',
        'input': $('#bookCategory'),
        'msg': 'กรุณาเลือกหมวดหมู่หนังสือ',
        'alert': $('#validate-bookCategory')
    }, {
        'formtype': 'text',
        'input': $('#auther'),
        'msg': 'ป้อนชื่อผู้แต่ง',
        'alert': $('#validate-auther')
    }, {
        'formtype': 'number',
        'name': 'edition',
        'input': $('#edition'),
        'msg': 'ใส่ครั้งที่พิมพ์',
        'alert': $('#validate-edition')
    }, {
        'formtype': 'text',
        'input': $('#publisherName'),
        'msg': 'ป้อนชื่อสำหนักพิมพ์',
        'alert': $('#validate-publisherName')
    }, {
        'formtype': 'number',
        'name': 'yearOfPublication',
        'input': $('#yearOfPublication'),
        'msg': 'เลือกปีที่พิมพ์',
        'alert': $('#validate-yearOfPublication')
    }, {
        'formtype': 'number',
        'input': $('#pageCount'),
        'msg': 'กรอกจำนวนหน้า',
        'alert': $('#validate-pageCount')
    }, {
        'formtype': 'number',
        'input': $('#price'),
        'msg': 'ป้อนราคา',
        'alert': $('#validate-bookPrice')
    }, {
        'formtype': 'file',
        'input': $('#bookImg'),
        'msg': 'เลือกรูปปกหนังสือ',
        'alert': $('#validate-bookImg')
    }]
    let emptyCount = 0

    bookForm.forEach((fd) => {
        const {
            alert,
            input,
            formtype,
            name
        } = fd
        let isValidate = false
        let msg = fd.msg
        if (formtype == 'text') {
            const v = input.val()
            if (v == '') {
                isValidate = true
                emptyCount++
            }
        }
        if (formtype == 'number') {
            const n = parseInt(input.val().trim())
            msg += ' หรือ ป้อนข้อมูลให้เป็นตัวเลข และค่าที่ไม่เท่า 0'
            if (isNaN(n) || n == 0) {
                isValidate = true
                emptyCount++
            } else {
                if (name == 'yearOfPublication') {
                    const l = String(n).length
                    if (l != 4) {
                        isValidate = true
                        emptyCount++
                        msg = 'กรุณาข้อมูลให้ครบ 4 หลัก'
                    }
                }
            }
        }
        if (formtype == 'file' && act == 'insert') {
            const f = input[0].files.length
            if (f == 0) {
                isValidate = true
                emptyCount++
            }
        }
        errValidate(isValidate, alert, msg)
    })
    const bookImage = $('#bookImg')[0].files[0]
    if (emptyCount == 0) {
        const fd = new FormData()
        fd.append('act', act)
        fd.append('publisher_name', $('#publisherName').val())
        fd.append('book_category', $('#bookCategory').val().filter((v) => v.trim() != '').join(','))
        fd.append('bookname', $('#bookname').val())
        fd.append('auther', $('#auther').val())
        fd.append('edition', $('#edition').val())
        fd.append('year_of_publication', $('#yearOfPublication').val())
        fd.append('page_count', $('#pageCount').val())
        fd.append('price', $('#price').val())
        fd.append('isbn', $('#isbn').val().trim())
        fd.append('barcode_no', $('#barcode').val().trim())
        if (bookImage) {
            fd.append('img', $('#bookImg')[0].files[0])
        }
        if (act == 'update') {
            fd.append('old_img', bookHandleSubmit().attr('data-oldimg'))
            fd.append('id', id)
        }

        $.ajax({
            url: './controller/bookController.php',
            type: 'post',
            contentType: false,
            processData: false,
            data: fd,
            complete: function (xhr, textStatus) {
                console.log(xhr.responseText)
                try {
                    const data = JSON.parse(xhr.responseText)
                    const isValidate = data.isValidate
                    if (xhr.status == 200) {
                        success('บันทึกข้อมูลเรียบร้อย')
                    } else {
                        errDialog('แจ้งเตือน', xhr.status, xhr.responseText)
                    }
                } catch (err) {
                    errDialog('เกิดข้อผิดพลาด', '', err)
                }

            }
        })
    }
})