function errValidate(isValidate, alert, msg) {
    alert.css('color', '#dc3545')
    if (isValidate) {
        alert.css('display', 'block')
    } else {
        alert.css('display', 'none')
    }
    alert.text(msg)
}