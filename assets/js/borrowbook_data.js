$('button[name="borrow-return"]').click(function () {
    const id = $(this).attr('data-id')
    confirmDialog('ยืนยันการคืนหนังสือ', 'คุณต้องการคืนหนังสือใช่ หรือไม่ ?')
        .then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: './controller/borrowController.php',
                    type: 'post',
                    data: {
                        'id': id,
                        'act': 'return'
                    },
                    complete: function (xhr, textStatus) {
                        console.log(xhr.responseText)
                        try {
                            if (xhr.status == 200) {
                                success('คืนหนังสือเรียบร้อย')
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
$('button[name="borrow-remove"]').click(function () {
    const id = $(this).attr('data-id')
    confirmDialog('ลบข้อมูลการจอง', 'คุณต้องการลบข้อมูลรายการนี้ใช่ หรือไม่ ?')
        .then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: './controller/borrowController.php',
                    type: 'post',
                    data: {
                        'id': id,
                        'act': 'delete'
                    },
                    complete: function (xhr, textStatus) {
                        console.log(xhr.responseText)
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

$('button[name="borrow-info"]').click(function () {
    const id = $(this).attr('data-id')
    $.ajax({
        url: './controller/borrowController.php',
        type: 'post',
        data: {
            'id': id
        },
        complete: function (xhr, textStatus) {
            try {
                const data = JSON.parse(xhr.responseText).borrow
                const borrowData = [
                    data.borrow_id,
                    data.book_name,
                    data.borrow_fname,
                    data.borrow_lname,
                    getOccupation(data.occupation),
                    getEducationLevel(data.education_level),
                    data.year_class,
                    data.branch,
                    data.contact_number,
                    data.borrow_date,
                    data.return_date,
                    data.borrow_officer,
                    data.return_officer,
                    data.status == 'borrow' ? 'ยืม' : 'คืนแล้วเรียบร้อย'
                ]

                if (xhr.status == 200) {
                    const textBorrowDetail = $('.text-borrowdetail')
                    for (let i = 0; i < textBorrowDetail.length; i++) {
                        let text = borrowData[i]

                        $(textBorrowDetail[i]).text(text)
                    }
                    $('#borrowTitle').text(borrowData[0])
                    $('#borrowInfoModal').modal('show')
                } else {
                    errDialog('แจ้งเตือน', xhr.status, xhr.responseText)
                }
            } catch (err) {
                errDialog('เกิดข้อผิดพลาด', '', err)
            }

        }
    })
})

document.addEventListener('DOMContentLoaded', () => {
    retainOption($('#status').attr('data-status'), $('#status'))
})
$('#findBorrowData').click(function () {
    let r = ``
    const start_dt = $('#startDate').val()
    const end_dt = $('#endDate').val()

    const name = $('#borrowByname').val()
    const status = $('#status').val()
    const is_startdate = start_dt != ''
    const is_enddate = end_dt != ''
    let is_validate = true
    let is_date = is_startdate || is_enddate

    if (is_date) {
        is_validate = false
        errValidate(!is_startdate, $('#validateStartDate'), 'กรุณาป้อนวันเริ่มต้น')
        errValidate(!is_enddate, $('#validateEndDate'), 'กรุณาป้อนวันสิ้นสุด')
        const start_stamp = getTimeStampNumber(start_dt)
        const end_stamp = getTimeStampNumber(end_dt)
        if (!isNaN(end_stamp) && !isNaN(start_stamp)) {
            if (end_stamp < start_stamp) {
                errDialog('แจ้งเตือน', '', 'กรุณาป้อนวันที่ให้ถูกต้อง')
                is_validate = false
            } else {
                r += `&start_dt=${start_dt}&end_dt=${end_dt}`
                is_validate = true
            }
        }


    }

    if (name != '') r += `&name=${name.replaceAll(' ', '-')}`
    if (status != '') r += `&status=${status}`
    if (r != '' && is_validate) {
        const route = `./?r=mborrow_book${r}`
        location.assign(route)
    }

})