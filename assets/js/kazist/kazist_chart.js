/* 
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */



kazist_chart = function () {
    return {
        chart_categories: '',
        loadChart: function (element, type, title, subtitle, data, legend) {


            if (typeof legend === 'undefined') {
                // foo could get resolved and it's defined
                legend = {
                    align: 'right',
                    layout: 'vertical',
                    verticalAlign: 'top',
                    floating: false
                }
            }

            if (type == 'area') {
                kazist_chart.areaChart(element, title, subtitle, data, legend);
            } else if (type == 'pie') {
                kazist_chart.pieChart(element, title, subtitle, data, legend);
            } else if (type == 'donut') {
                kazist_chart.donutChart(element, title, subtitle, data, legend);
            } else {
                kazist_chart.barChart(element, title, subtitle, data, legend);
            }

        }, barChart: function (element, title, subtitle, data, legend) {

            var categories = kazist_chart.getTextFromData(data);
            var values = kazist_chart.getValueFromData(data);
            var series_arr = kazist_chart.getSeriesFromData(data);


            element.highcharts({
                chart: {
                    type: 'column'
                },
                title: {
                    text: title
                },
                subtitle: {
                    text: subtitle
                },
                xAxis: {
                    categories: ['Chart'],
                    crosshair: true
                },
                legend: legend,
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
                    footerFormat: '</table>',
                    shared: false,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: series_arr


            });
        }, areaChart: function (element, title, subtitle, data, legend) {

            var categories = kazist_chart.chart_categories;
            var values = kazist_chart.getValueFromData(data);
            var series_arr = kazist_chart.getSeriesFromData(data);


            element.highcharts({
                chart: {
                    type: 'area'
                },
                title: {
                    text: title
                },
                xAxis: {
                    categories: categories
                },
                subtitle: {
                    text: subtitle
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
                    footerFormat: '</table>',
                    shared: false,
                    useHTML: true
                },
                series: data


            });

        }, pieChart: function (element, title, subtitle, data, legend) {

            // Build the chart
            element.highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: title
                },
                legend: legend,
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    }
                },
                series: [{
                        name: subtitle,
                        colorByPoint: true,
                        data: data
                    }]
            });

        }, donutChart: function (element, title, subtitle, data, legend) {

            element.highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: 0,
                    plotShadow: false
                },
                title: {
                    text: title,
                    align: 'center',
                    verticalAlign: 'middle',
                    y: 40
                },
                legend: legend,
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        dataLabels: {
                            enabled: true,
                            distance: -50,
                            style: {
                                fontWeight: 'bold',
                                color: 'white',
                                textShadow: '0px 1px 2px black'
                            }
                        },
                        startAngle: -90,
                        endAngle: 90,
                        center: ['50%', '75%']
                    }
                },
                series: [{
                        type: 'pie',
                        name: subtitle,
                        innerSize: '50%',
                        data: data
                    }]
            });

        }, getTextFromData: function (data) {

            var text = [];

            jQuery.each(data, function (index, value) {
                text.push(value[0]);
            });

            return text;

        }, getValueFromData: function (data) {
            var value_arr = [];

            jQuery.each(data, function (index, value) {
                value_arr.push(value[1]);
            });

            return value_arr;
        }, getSeriesFromData: function (data) {
            var series_arr = [];

            jQuery.each(data, function (index, value) {
                var series_obj = {
                    //showInLegend: false,
                    name: value[0],
                    data: [value[1]]
                }
                series_arr.push(series_obj);
            });

            return series_arr;
        }



    };
}();