function getLocationPage() {
    const href = location.href
    const url = new URL(href)
    const params = url.searchParams
    const page_type = params.get('r')
    return `./index.php?r=${page_type}&page=0`
}

$('#pagination-query').change(function () {
    const value = $(this).val()
    console.log(value)
    if (value != '') {
        location.assign(`${getLocationPage()}&per_page=${value}`)
    }
})