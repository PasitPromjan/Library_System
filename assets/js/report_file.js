function getStartDate() {
    return $('#startDate')
}

function getEndDate() {
    return $('#endDate')
}
$('#findReportFileBtn').click(function () {
    console.log('ค้นหา')
    const start_dt = $('#startDate').val()
    const end_dt = $('#endDate').val()
    const fileType = $('#fileType').val()
    const isFindByDate = start_dt != '' || end_dt != ''
    let isDateValidate = true
    let validateCount = 0

    if (isFindByDate) {
        const findReportForm = [{
            'input': $('#startDate'),
            'validate': $('#validateStartDate'),
            'msg': 'กรุณาเลือกวันที่เริ่มต้น'
        }, {
            'input': $('#endDate'),
            'validate': $('#validateEndDate'),
            'msg': 'กรุณาเลือกวันที่สิ้นสุด'
        }]

        findReportForm.forEach(fd => {
            const v = fd.input.val()
            let isValidate = false
            if (v == '') {
                validateCount++
                isValidate = true
            }
            errValidate(isValidate, fd.validate, fd.msg)
        })
        isDateValidate = validateCount == 0
    }

    if (isDateValidate) {
        const start_stamp = getTimeStampNumber(start_dt)
        const end_stamp = getTimeStampNumber(end_dt)
        if (end_stamp < start_stamp) {
            isDateValidate = false
            errDialog('แจ้งเตือน', 'โปรดเลือกเวลาที่ถูกต้อง', 'วันเริ่มต้นต้องเป็นวันก่อนหรือเท่ากับวันที่สิ้นสุด')
        }
    }

    if (isDateValidate) {

        let route = ''
        if (start_dt != '' && end_dt != '') {
            route += `&start_dt=${start_dt}&end_dt=${end_dt}`
        }
        route += fileType != '' ? `&filetype=${fileType}` : ''
        if (route != '') {
            route = `./index.php?r=report_file${route}`
            location.assign(route)
        }
    }
})
$('[name="filereport-remove"]').click(function () {
    const id = $(this).attr('data-id')
    confirmDialog('ลบไฟล์รายงาน', 'ต้องการลบลบไฟล์รายการนี้ใช่ หรือไม่?')
        .then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: './controller/reportFileController.php',
                    type: 'post',
                    data: {
                        'id': id,
                        'act': 'delete'
                    },
                    complete: function (xhr, textStatus) {
                        try {
                            const data = JSON.parse(xhr.responseText)
                            if (xhr.status == 200) {
                                success('ลบข้อมูลเรียบร้อย')
                            } else {
                                errDialog('แจ้งเตือน', '', xhr.responseText)
                            }
                        } catch (err) {
                            console.log(err)
                            errDialog('ข้อผิดพลาด', '', err)
                        }
                    }
                })

            }
        });
})