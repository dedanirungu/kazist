/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function () {
    var d = new Date();

    var curr_date = d.getDate();
    var curr_month = d.getMonth();
    var curr_year = d.getFullYear();

    var date_str = curr_year + '-' + curr_month + '-' + curr_date;

    var cal_url_params = {
        app: 'users',
        com: 'todolist',
        subset: 'todolist',
        task: 'calendarjson',
        controller: 'TodolistController',
        description: 'To do List',
        show_link: '1'
    }

    jQuery('#todolist_calendar').fullCalendar({
        header: {
            left: 'prev,next',
            center: 'title',
            right: 'month'
        },
        defaultDate: date_str,
        editable: true,
        events: {
            url: kazicode.url,
            type: 'GET',
            data: cal_url_params,
            error: function () {
                alert('there was an error while fetching events!');
            }
        },
        eventRender: function (event, element) {
            element.attr('title', event.tip);
            element.attr('data-html', 'true');
            element.tooltip();
        },
        eventClick: function (event) {
            if (event.url) {
                window.open(event.url, '_self');
                return false;
            }
        }
    });
});

