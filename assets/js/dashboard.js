const bookBarChart = $('#bookBarChart')
const bookData = JSON.parse(atob(bookBarChart.attr('data-book')))
const areaChartData = {
    labels: bookData.book_name,
    datasets: [{
        label: 'จำนวน',
        backgroundColor: [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'
        ],
        pointRadius: false,
        pointColor: '#3b8bba',
        pointStrokeColor: 'rgba(60,141,188,1)',
        pointHighlightFill: '#fff',
        pointHighlightStroke: 'rgba(60,141,188,1)',
        data: bookData.count
    }
    ]
}

const bookBarChartData = $.extend(true, {}, areaChartData)

const barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    datasetFill: false
}

new Chart(bookBarChart, {
    type: 'doughnut',
    data: bookBarChartData,
    options: barChartOptions
})

Object.values(categorySortByMonth).forEach((d) => {
    const bookBarChart = $(`#categoryBarChart-${d.date}`)
    const areaChartData = {
        labels: d.category,
        datasets: [{
            label: 'จำนวน',
            backgroundColor: ['rgb(255, 99, 132)'],
            borderColor: 'rgba(255, 99, 132,0.8)',
            pointRadius: false,
            pointColor: 'rgba(255, 99, 132)',
            pointStrokeColor: 'rgba(255, 99, 132,1)',
            pointHighlightFill: '#fff',
            pointHighlightStroke: 'rgba(255, 99, 132,1)',
            data: d.count
        },


        ]
    }

    const bookBarChartData = $.extend(true, {}, areaChartData)

    const barChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false,
        indexAxis: 'y',
        elements: {
            bar: {
                borderWidth: 2,
            }
        },
        responsive: true,
    }

    new Chart(bookBarChart, {
        type: 'bar',
        data: bookBarChartData,
        options: barChartOptions
    })
})

Object.values(dataBookSortByMonth).forEach((d) => {
    const bookBarChart = $(`#bookBarChart-${d.date}`)
    const areaChartData = {
        labels: d.bookname,
        datasets: [{
            label: 'จำนวน',
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgba(255, 99, 132,0.8)',
            pointRadius: false,
            pointColor: 'rgba(255, 99, 132)',
            pointStrokeColor: 'rgba(255, 99, 132,1)',
            pointHighlightFill: '#fff',
            pointHighlightStroke: 'rgba(255, 99, 132,1)',
            data: d.count
        }]
    }

    const bookBarChartData = $.extend(true, {}, areaChartData)

    const barChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false,
        indexAxis: 'y',
        elements: {
            bar: {
                borderWidth: 2,
            }
        },
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
            },
            title: {
                display: true,
                text: 'Chart.js Horizontal Bar Chart'
            }
        }


    }

    new Chart(bookBarChart, {
        type: 'bar',
        data: bookBarChartData,
        options: barChartOptions
    })
})
