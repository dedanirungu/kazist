/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function () {

    var height = jQuery("body").height();
    var already_show_popup = false;

    analyzeCookies();

    setTimeout(function () {
        showPopup();
    }, 10000);


    function analyzeCookies() {

        var date = new Date();
        var hours = 3;
        var cookie = kazist.readCookie('kazi-popup-block');

        if (!cookie) {
            kazist.createCookie('kazi-popup-block', true, date);
        } else {
            already_show_popup = true;
        }
    }

    function showPopup() {
        if (!already_show_popup) {
            jQuery('#pop-block').modal('show');
            already_show_popup = true;
        }
    }

    function addEvent(obj, evt, fn) {
        if (obj.addEventListener) {
            obj.addEventListener(evt, fn, false);
        }
        else if (obj.attachEvent) {
            obj.attachEvent("on" + evt, fn);
        }
    }

    addEvent(window, "load", function (e) {
        addEvent(document, "mouseout", function (e) {
            e = e ? e : window.event;
            var from = e.relatedTarget || e.toElement;
            if (!from || from.nodeName == "HTML") {
                // stop your drag event here
                // for now we can just use an alert
                showPopup();
            }
        });
    });

})


