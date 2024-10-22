function getNumberFormat(number) {
    const n = new Intl.NumberFormat('th').format(
        number,
    )
    return n.includes('.') ? n : n + '.00'
}


function getSumNumber(data) {
    return data.reduce((current, prev) => current + prev, 0)
}
function retainRadio(value, optionList) {
    $.each(optionList, (i, opt) => {
        if ($(opt).val() == value) {
            $(opt).prop('checked', true)
        }
    })
}


function getTimeStampNumber(date) {
    return new Date(`${date} 00:00:00`).valueOf()
}
function getTimeStampByDateAndTime(date) {
    return new Date(date).valueOf()
}

function getTimeStampByDateNow() {
    return new Date().valueOf()-60000
}
function getCountDate(date) {
    return date.toString().length == 2 ? date : `0${date}`
}
function getDateNow() {
    const date = new Date()
    const dt = getCountDate(date.getDate())
    const m = getCountDate(date.getMonth() + 1)
    const y = date.getFullYear()
    return `${y}-${m}-${dt}`
}

function getTimeStampNow() {
    return new Date(`${getDateNow()} 00:00:00`).valueOf()
}

function getCountDayOfMonth(y, m) {
    return new Date(y, m, 0).getDate()
}
function isDateStartMonth(start) {
    return Number(start.split('-')[2]) == 1
}

function isDateEndMonth(end) {
    const _end = end.split('-')
    const _dt = Number(_end[2])
    const c = getCountDayOfMonth(Number(_end[0]), Number(_end[1]))
    return c == _dt
}

function getCountMonth(start, end) {
    const s = start.split('-')
    const e = end.split('-')
    const sm = Number(s[1])
    const em = Number(e[1])
    const isTwoMonth = em - sm
    return isTwoMonth < 2
}

function validatePassword(pass) {
    let upper = 0
    let lower = 0
    let num = 0
    let thaiLang = 0
    let alert = ''
    let validate = true

    if (pass.length < 8) {
        validate = false
        alert = 'รหัสผ่านต้องมีอักขระอย่างน้อย 8 ตัว'
    } else {
        for (let i = 0; i < pass.length; i++) {
            const text = pass[i]
            const char = /[a-zA-Z]/.test(text)
            const n = /\d/.test(text)
            const thai_letter = /[ก-ฮะ-์]/.test(text)
            if (char) {
                if (text.toUpperCase() == text) {
                    console.log('A')
                    upper++
                }
                if (text.toUpperCase() != text) {
                    console.log('a')
                    lower++
                }
            }
            if (n) {
                num++
            }
            if (thai_letter) {
                thaiLang++
            }
        }
        if (thaiLang > 0) {
            validate = false
            alert = 'รหัสผ่านต้องใช้เป็นภาษาอังกฤษเท่านั้น'
        } else {
            if (upper < 1 || lower < 1 || num < 1) {
                validate = false
                alert = 'รหัสผ่านต้องประกอบ อักขระตัวพิมพ์เล็ก พิมพ์ใหญ่'
            }
        }
    }
    return { validate, alert }
}

function obscureText(input) {
    const element = $(input)
    const isType = element.attr('type') == 'text' ? 'password' : 'text'
    element.attr('type', isType)
}
function hideErrValidate() {
    $('.err-validate').css('display', 'none')
}

function retainOption(value, optionList) {
    $.each(optionList.children(), (i, opt) => {
        if ($(opt).val() == value.trim()) {
            $(opt).prop('selected', true)
        }
    })
}

function getEducationLevel(level) {
    const educationDict = {
        'elementary': 'ประถมศึกษา',
        'secondaryEducation': 'มัธยมศึกษา',
        'bachelor': 'ปริญญาตรี',
        'master': 'ปริญญาโท',
        'philosophy': 'ปริญญาเอก',
        'vocationalCertificate': 'ระดับประกาศนียบัตรวิชาชีพ',
        'higherVocationalCertificate': 'ประกาศนียบัตรวิชาชีพชั้นสูง'
    }
    const has = Object.keys(educationDict).indexOf(level)
    return has >= 0 ? Object.values(educationDict)[has] : ''
}
function getOccupation(occup) {
    const OccupationDict = {
        'student': 'นักเรียน - นักศึกษา',
        'teacher': 'ครู - อาจารย์'
    }
    const has = Object.keys(OccupationDict).indexOf(occup)
    return has >= 0 ? Object.values(OccupationDict)[has] : occup
}

function getParam(search) {
    return new URLSearchParams(search)
}

