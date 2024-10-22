$('button[name="book-remove"]').click(function () {
    const id = $(this).attr('data-id')
    confirmDialog('ลบข้อมูลหนังสือ', 'คุณต้องการลบข้อมูลรายการนี้ใช่ หรือไม่ ?')
        .then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: './controller/bookController.php',
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
$('button[name="book-info"]').click(function () {
    const id = $(this).attr('data-id')
    $.ajax({
        url: './controller/bookController.php',
        type: 'post',
        data: {
            'id': id
        },
        complete: function (xhr, textStatus) {
            try {
                const data = JSON.parse(xhr.responseText).book
                console.log(data)
                const bookData = [
                    data.book_id,
                    data.isbn,
                    data.book_name,
                    data.category,
                    data.auther,
                    data.edition,
                    data.publisher_name,
                    data.year_of_publication,
                    data.page_count,
                    data.price,
                    data.create_at,
                    data.update_at
                ]
                const bookInfoText = $('.book-info-text')
                if (xhr.status == 200) {

                    for (let i = 0; i < bookInfoText.length; i++) {
                        let text = bookData[i]
                        $(bookInfoText[i]).text(text)
                    }
                    const img = `./assets/book_img/` + data.book_img
                    $('#bookInfoImage').attr('src', img)
                    $('#bookTitle').text(bookData[0])
                    $('#bookInfoModal').modal('show')
                } else {
                    errDialog('แจ้งเตือน', xhr.status, xhr.responseText)
                }
            } catch (err) {
                console.error(err)
                errDialog('เกิดข้อผิดพลาด', '', err)
            }

        }
    })
})

$('#book-submit').click(function () {
    const v = $('#book-search').val().trim().replaceAll(' ', '-')
    if (v != '') {
        window.location.assign(`./?r=book_data&n=${v}`)
    }
})

$('#book-search').keyup(function (e) {
    const v = $(this).val().trim().replaceAll(' ', '-')
    const keyCode = e.keyCode
    if (keyCode == 13) {
        if (v != '') {
            window.location.assign(`./?r=book_data&n=${v}`)
        }
    }

})
