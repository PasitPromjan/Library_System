function createSelect2Remote(selector, url, noResultsText, loadingText) {
    $(selector).select2({
        language: {
            searching: function () {
                return "กำลังค้นหาข้อมูล";
            },
            noResults: function () {
                return noResultsText;
            },
            errorLoading: function () {
                return loadingText
            },

        },
        ajax: {
            delay: 300,
            url: url,
            dataType: "json",
            type: "POST",
            data: function (params) {

                var queryParameters = {
                    search: params.term
                }
                return queryParameters;
            },
            processResults: function (data) {
                console.log(data)
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.name,
                            id: item.id,
                        }
                    })
                };
            },
            complete(xhr, textStatus) {
                console.log(xhr.responseText)
            }
        }
    });
}