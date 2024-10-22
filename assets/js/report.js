
function getReportStartDate() {
    return $('#reportStartDate')
}
function getReportEndDate() {
    return $('#reportEndDate')
}

function getBorrowStatus() {
    return $('#status')
}
document.addEventListener('DOMContentLoaded', () => {
    retainOption($('#status').attr('data-status'), $('#status'))
})

function findDataByDate() {
    let r = ``
    const start_dt = getReportStartDate().val()
    const end_dt = getReportEndDate().val()
    const is_startdate = start_dt != ''
    const is_enddate = end_dt != ''
    let is_date = is_startdate || is_enddate

    if (is_date) {
        errValidate(!is_startdate, $('#validateStartDate'), 'กรุณาป้อนวันเริ่มต้น')
        errValidate(!is_enddate, $('#validateEndDate'), 'กรุณาป้อนวันสิ้นสุด')
        const start_stamp = getTimeStampNumber(start_dt)
        const end_stamp = getTimeStampNumber(end_dt)
        if (!isNaN(end_stamp) && !isNaN(start_stamp)) {
            if (end_stamp < start_stamp) {
                errDialog('แจ้งเตือน', '', 'กรุณาป้อนวันที่ให้ถูกต้อง')
            } else {
                r += `&start_dt=${start_dt}&end_dt=${end_dt}`
            }
        }
    }

    return { is_enddate, is_startdate, is_date, r }
}

$('#findReportBtn').click(function () {
    const status = getBorrowStatus().val()
    let { is_enddate, is_startdate, is_date, r } = findDataByDate()
    let p = ''
    let is_valid = true
    if (status != '') p += `&status=${status}`
    if (r != '') p += r

    if (is_date && (!is_enddate || !is_startdate)) {
        is_valid = false
    }
    if (is_valid && p != '') {
        const route = `./?r=report${p}`
        location.assign(route)
    }

})

$('button[name="report-to-file"]').click(function () {
    const filetype = $(this).attr('data-file')
    const { r } = findDataByDate()
    const p = getParam(r)
    const start = p.get('start_dt')
    const end = p.get('end_dt')
    if (!start && !end) {
        errDialog('แจ้งเตือน', '', 'โปรดป้อนวันที่ในการค้นหา')
    }
    if (r != '') {
        const url = filetype == 'pdf'
            ? './controller/pdfController.php'
            : './controller/excelController.php';
        $.ajax({
            url: url,
            type: 'post',
            data: {
                'start_dt': start,
                'end_dt': end
            },
            complete: function (xhr, textStatus) {
                try {
                    const data = JSON.parse(xhr.responseText)
                    if (xhr.status == 200) {
                        window.open('./assets/' + data.file_target, '_blank')
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