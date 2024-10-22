$('[name="occup-opt"]').change(function () {
    const v = $(this).val().trim()
    console.log(v)
    let isDisabled = v != 'student'
    let isOccup = v != 'other'
    $('[name="year-class"]').prop('disabled', isDisabled)
    $('[name="education-level"]').prop('disabled', isDisabled)
    $('[name="branch-option"]').prop('disabled', !isOccup)
    $('#occup').prop('disabled', isOccup)
})

$('[name="branch-option"]').change(function () {
    const v = $(this).val().trim()
    let isDisabled = v != 'true'
    $('#branch').prop('disabled', isDisabled)
})



document.addEventListener('DOMContentLoaded', () => {
    retainRadio($('#occupRetain').val().trim(), $('[name="occup-opt"]'))
    retainRadio($('#educationLevelRetain').val().trim(), $('[name="education-level"]'))
    retainRadio($('#yearClassRetain').val().trim(), $('[name="year-class"]'))
    $('[name="education-level"]').prop('disabled', $('#educationLevelRetain').val() == '')
    $('[name="year-class"]').prop('disabled', $('#yearClassRetain').val() == '')
    retainRadio($('#branch').val().trim() != '' ? 'true' : 'false', $('[name="branch-option"]'))
})




$('#officernameOption').change(function () {
    const v = $(this).val().trim()
    $('#officerName').prop('disabled', v != '')
})

function borrowHandleSubmit() {
    return $('#borrowHandleSubmit')
}
$('#borrowHandleSubmit').click(function () {
    const borrowForm = [
        {
            'formtype': 'text',
            'input': $('#bookname'),
            'alert': $('#validate-bookname'),
            'msg': 'เลือกหนังสือก่อน'
        }, {
            'formtype': 'text',
            'input': $('#borrowlname'),
            'alert': $('#validate-borrowfname'),
            'msg': 'กรุณาป้อนชื่อผู้ยืม'
        }, {
            'formtype': 'text',
            'name': 'borrowname',
            'input': $('#borrowlname'),
            'alert': $('#validate-borrowlname'),
            'msg': 'กรุณาป้อนนนามสกุลผู้ยืม'
        },
        {
            'formtype': 'branch',
            'name': 'branch',
            'input': $('[name="branch-option"]'),
            'alert': $('#validate-branch'),
            'msg': 'กรุณาเลือกสาขา'
        },
        {
            'formtype': 'text',
            'input': $('#contact'),
            'alert': $('#validate-contact'),
            'msg': 'กรุณาระบุเบอร์ติดต่อ'
        },

        {
            'formtype': 'text',
            'input': $('#borrowDate'),
            'alert': $('#validate-borrowDate'),
            'msg': 'กรุณาระบุวันที่ และเวลาที่ยืม'
        }, {
            'formtype': 'time',
            'input': [$('#borrowHour'), $('#borrowMinute')],
            'alert': $('#validate-borrowTime'),
            'msg': 'กรุณาระบุวันที่ และเวลาที่ยืม'
        }

        , {
            'formtype': 'text',
            'input': $('#returnDate'),
            'alert': $('#validate-returnDate'),
            'msg': 'กรุณาระบุวันที่ และเวลาที่ต้องคืน'
        }, {
            'formtype': 'time',
            'input': [$('#returnHour'), $('#returnMinute')],
            'alert': $('#validate-returnTime'),
            'msg': 'กรุณาระบุวันที่ และเวลาที่ต้องคืน'
        }, {
            'formtype': 'occup',
            'input': $('[name="occup-opt"]'),
            'alert': $('#validate-occup'),
            'msg': 'กรุณาเลือกอาชีพ'
        }
    ]
    let emptyCount = 0
    borrowForm.forEach((fd) => {
        const { input, formtype, alert } = fd
        let msg = fd.msg
        let isValidate = false
        if (formtype == 'text' && input.val().trim() == '') {
            isValidate++
            emptyCount++
        }
        if (formtype == 'radio' && input.filter(':checked').length == 0) {
            isValidate++
            emptyCount++
        }

        if (formtype == 'time') {
            const h = input[0].val().trim()
            const minute = input[1].val().trim()
            if (h == '' || minute == '') {
                isValidate++
                emptyCount++
            }
        }
        if (formtype == 'occup') {
            const occup = input.filter(':checked').val()
            let isYearClass = false
            let isEducation = false
            if (!occup) {
                emptyCount++
                isValidate = true
            } else {
                if (occup == 'student') {
                    const year_class = $('[name="year-class"]').filter(':checked').length
                    const education = $('[name="education-level"]').filter(':checked').length
                    if (year_class == 0) isYearClass = true
                    if (education == 0) isEducation = true
                }
                if (occup == 'other' && $('#occup').val().trim() == '') {
                    emptyCount++
                    isValidate = true
                    msg = 'กรุณาป้อนอาชีพ'
                }
            }
            errValidate(isYearClass, $('#validate-yearclass'), 'กรุณาเลือกชั้นปี')
            errValidate(isEducation, $('#validate-education'), 'กรุณาเลือกระดับชั้น')
        }

        if (formtype == 'branch') {
            const l = input.filter(':checked').val() == 'true'
            if (l) {
                if ($('#branch').val().trim() == '') {
                    emptyCount++
                    isValidate = true
                }
            }
        }
        errValidate(isValidate, alert, msg)
    })


    const borrowTime = `${$('#borrowHour').val()}:${$('#borrowMinute').val()}`
    const returnTime = `${$('#returnHour').val()}:${$('#returnMinute').val()}`

    const borrowDate = $('#borrowDate').val()
    const returnDate = $('#returnDate').val()

    const borrowTimeStamp = getTimeStampByDateAndTime(`${borrowDate} ${borrowTime}:00`)
    const returnTimeStamp = getTimeStampByDateAndTime(`${returnDate} ${returnTime}:00`)
    const dateNow = getTimeStampByDateNow()


    if (emptyCount == 0) {
        const act = borrowHandleSubmit().attr('data-act')
        const id = borrowHandleSubmit().attr('data-id')
        let msg = ''
        let is_datevalidate = true
        if (act == 'insert') {
            if (borrowTimeStamp < dateNow) {
                msg = 'โปรดเลือกเวลาปัจจุบัน'
                is_datevalidate = false
            }
        }
        if (borrowTimeStamp >= dateNow) {
            if (borrowTimeStamp > returnTimeStamp) {
                msg = 'โปรดป้อนข้อมูลเวลาให้ถูกต้อง วันและเวลาคืน ต้องมากกว่ายืม'
                is_datevalidate = false
            }
        }
        if (!is_datevalidate) {
            errDialog('แจ้งเตือน', msg, '')
        }
        const fd = new FormData()

        let occup = $('[name="occup-opt"]').filter(':checked').val()
        let branch = $('[name="branch-option"]')
            .filter(':checked').val() == 'true'
            ? $('#branch').val().trim()
            : '';
        let educationLevel = ''
        let yearclass = ''
        let officername = $('officernameOption').val()
        if (officername != '') {
            officername = $('#officerName').val()
        }
        if (occup == 'other') {
            occup = $('#occup').val().trim()
        }
        if (occup == 'student') {
            educationLevel = $('[name="education-level"]').filter(':checked').val()
            yearclass = $('[name="year-class"]').filter(':checked').val()
        }

        fd.append('act', act)
        fd.append('bookname', $('#bookname').val().trim())
        fd.append('borrow_fname', $('#borrowfname').val().trim())
        fd.append('borrow_lname', $('#borrowlname').val().trim())
        fd.append('branch', branch)
        fd.append('occup', occup)
        fd.append('contact_number', $('#contact').val())
        fd.append('education_level', educationLevel)
        fd.append('year_class', yearclass)
        fd.append('borrow_date', $('#borrowDate').val())
        fd.append('borrow_time', borrowTime)
        fd.append('return_date', $('#returnDate').val())
        fd.append('return_time', returnTime)
        fd.append('officer', officername)

        if (act == 'update') {
            fd.append('id', id)
        }

        if (is_datevalidate) {
            $.ajax({
                url: './controller/borrowController.php',
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                complete: function (xhr, textStatus) {
                    console.log(xhr.responseText)
                    try {
                        const data = JSON.parse(xhr.responseText)
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

    }
})


function select2Option() {
    return {

        searching: function () {
            return "กำลังค้นหาข้อมูล";
        },
        "noResults": function () {
            return "ไม่พบข้อมูล";
        },
    }

}
$('#borrowHour').select2({ language: select2Option() })
$('#borrowMinute').select2({ language: select2Option() })
$('#returnHour').select2({ language: select2Option() })
$('#returnMinute').select2({ language: select2Option() })
createSelect2Remote("#bookname", './controller/bookController.php', 'ไม่พบหนังสือที่ต้องการค้นหา', 'ป้อนชื่อหนังสือ')