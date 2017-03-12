/* 
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

jQuery(document).ready(function () {

    jQuery("input[type='checkbox'], input[type='radio']").on('ifChecked', function () {

        var input = jQuery(this).find('input');

        if (input.is(':checked')) {
            input.attr('checked', 'checked');
        } else {
            input.removeAttr('checked');
        }
    });

    jQuery('.select-button .btn').click(function () {
        onClickSelectButtons(jQuery(this));
    });

    kazist.init();


});

function onClickSelectButtons(this_element) {

    var group_btn = this_element.closest('.btn-group');
    var this_btn = this_element.closest('.btn');

    group_btn.find(".btn").removeClass("active").removeClass("btn-primary").addClass('btn-default');
    group_btn.find('input').removeAttr('checked');

    this_btn.addClass('btn-primary').removeClass('btn-default');
    this_btn.find('input').attr('checked', 'checked');

}

kazist = function () {
    return {
        init: function () {
            kazist.addEvents(jQuery('body'));
            //jQuery(window).resize(kazist.toggleNavbarMethod);

        }, addEvents: function (html) {

            html.find('.item-yesno').on('click', function () {
                this_element = jQuery(this);
                return kazist_adminevents.yesNoStatusUpdate(this_element);
            });

            html.find('.pagination-select').change(function () {
                jQuery(this).closest('form').submit();
                console.log(jQuery(this).closest('form'));
            });


            html.find("input[type='checkbox'], input[type='radio']").on('ifChecked', function () {
                var input = jQuery(this).find('input');
                if (input.is(':checked')) {
                    input.attr('checked', 'checked');
                } else {
                    input.removeAttr('checked');
                }
            });

            html.find('.select_all_cid').click(function () {

                if (this.checked) {
                    // Iterate each checkbox
                    jQuery(this).closest('table').find('.cid').each(function () {
                        this.checked = true;
                    });
                } else {
                    jQuery(this).closest('table').find('.cid').each(function () {
                        this.checked = false;
                    });
                }
            });

            html.find('.confirm_delete').click(function () {

                var confirm_delete = confirm('Are you sure you want to delete selected record.');

                if (!confirm_delete) {
                    return  kazist.confirmDelete();
                }
                return false;
            });

            html.find('.kazi-delete').click(function () {
                kazist.setViewNSubmit('delete');
            });

            html.find('.kazi-add').click(function () {

                jQuery('.cid').prop('checked', false);
                kazist.setViewNSubmit('add', 'save');
            });

            html.find('.kazi-delete').click(function () {
                kazist.setViewNSubmit('delete');
            });

            html.find('.kazi-detail').click(function () {
                kazist.setViewNSubmit('detail');
            });

            html.find('.kazi-edit').click(function () {
                kazist.setViewNSubmit('edit');
            });

            html.find('.kazi-save').click(function () {
                kazist.setSubmit('save');
            });

            html.find('.kazi-savenew').click(function () {
                kazist.setViewNSubmit('save', 'savenew');
            });

            html.find('.kazi-savecopy').click(function () {
                kazist.setViewNSubmit('save', 'savecopy');
            });

            html.find('.kazi-saveclose').click(function () {
                kazist.setViewNSubmit('save', 'saveclose');
            });

            html.find('.kazi-cancel').click(function () {
                window.history.back();
                return false;
            });

            html.find('.kazi-print').click(function () {
                window.print();
                return false;
            });

            html.find('.select-button .btn').click(function () {
                kazist.onClickSelectButtons(jQuery(this));
            });

            html.find('.cancel-search').click(function () {
             
                var form = jQuery(this).closest('form');
                var input = form.find('input:text, input:radio, select');

                input.each(function () {
                    jQuery(this).val('');
                });

                form.submit();
            });

            kazist.initializeEditor();


        }, initializeEditor: function () {

            kazi_editor_height = jQuery('textarea.kazi-editor').attr('height');

            is_tinyMCE_active = false;

            if (typeof (tinyMCE) != "undefined") {
                if (tinyMCE.activeEditor == null || tinyMCE.activeEditor.isHidden() != false) {
                    is_tinyMCE_active = true;
                }
            }
            // alert( kazist_document.web_root); 

            if (is_tinyMCE_active) {

                tinymce.init({
                    selector: "textarea.kazi-editor",
                    plugins: [
                        "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                        "table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern fullscreen"
                    ],
                    toolbar: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify  | fontselect fontsizeselect | forecolor backcolor | " +
                            "cut copy paste searchreplace | hr | bullist numlist | outdent indent | undo redo | link unlink image media | table",
                    toolbar_items_size: 'small',
                    document_base_url: kazist_document.web_root,
                    powerpaste_word_import: 'clean',
                    powerpaste_html_import: 'merge',
                    file_browser_callback: function (field_name, url, type, win) {

                        if (type === 'file') {

                            jQuery('#linkModal').css('z-index', '100000');
                            jQuery('#linkModal').modal('show');
                            jQuery('#linkModal .kazicode_link_source').val('editor');
                            jQuery('#linkModal .kazicode_link_search_type').val(type);
                            jQuery('#linkModal .kazicode_link_input_id').val(field_name);

                        } else {

                            jQuery('#mediaModal').css('z-index', '100000');
                            jQuery('#mediaModal').modal('show');
                            jQuery('#mediaModal .kazicode_media_source').val('editor');
                            jQuery('#mediaModal .kazicode_media_search_type').val(type);
                            jQuery('#mediaModal .kazicode_media_input_id').val(field_name);

                        }
                    }
                });

            }
        }, setSubmit: function () {
            var main_form_id = jQuery('#main_form_id').val();

            jQuery('#' + main_form_id).submit();

        }, setViewNSubmit: function (view_name, task_name) {

            var base_route = kazist_document.document.base_route;
            var main_form_id = jQuery('#main_form_id').val();
            var url = kazist_document.web_base + '/' + base_route.split('.').join('/');

            if (view_name != '' && typeof view_name != 'undefined') {
                url += '/' + view_name;
            }

            if (task_name != '' && typeof task_name != 'undefined') {
                url += '?activity=' + task_name;
            }

            jQuery('#' + main_form_id).attr('action', url);

            jQuery('#' + main_form_id).submit();

            return false;

        }, confirmDelete: function () {

            var confirm_delete = confirm('Are you sure you want to delete selected record.');

            if (!confirm_delete) {
                return false;
            }

        }, onClickSelectButtons: function (this_element) {

            var group_btn = this_element.closest('.btn-group');
            var this_btn = this_element.closest('.btn');

            group_btn.find(".btn").removeClass("active").removeClass("btn-primary").addClass('btn-default');
            group_btn.find('input').prop('checked', false);

            this_btn.addClass('btn-primary').removeClass('btn-default');
            this_btn.find('input').prop('checked', true);

        }, callAjax: function (url, data_object, wait_to_complete) {

            return kazist_ajax.callAjax(url, data_object, wait_to_complete);

        }, callAjaxByRoute: function (route, data_object, wait_to_complete) {

            var url = kazist_document.web_base;

            route = route.split('.').join('/');
            url += '/' + route;


            return kazist_ajax.callAjax(url, data_object, wait_to_complete);

        }, loadChart: function (element, type, title, subtitle, data, legend) {
            kazist_chart.loadChart(element, type, title, subtitle, data, legend);
        },
        loadScriptByUrl: function (url) {

            jQuery.ajax({
                url: url,
                dataType: 'script',
                async: true,
                cache: false
            }).done(function () {
                // jQuery.cookie("cookie_name", "value", {expires: 7});
            }).fail(function () {
                var message = ' Script for following url failed to load<br><br>' + url;
                var title = 'Loading Script Failed';
                kazist.showDialog(title, message, 'error');
            });

        }, showDialog: function (title, message, type, width, height) {


            width = typeof width !== 'undefined' ? width : 300;
            height = typeof height !== 'undefined' ? height : 200;

            var class_name = '';
            if (type === 'error' || type === 'danger') {
                class_name = 'btn-danger';
            } else if (type === 'info' || type === 'success') {
                class_name = 'btn-success';
            }

            jQuery("<div>" + message + "</div>").dialog(
                    {
                        title: title,
                        height: height,
                        width: width,
                        'class': "mydialog btn btn-primary",
                        create: function () {
                            jQuery(this).closest(".ui-dialog")
                                    .find(".ui-dialog-titlebar-close") // the first button
                                    //.removeClass("ui-dialog-titlebar-close") // the first button
                                    .addClass("btn btn-danger btn-xs fa fa-times ");
                            jQuery(this).closest(".ui-dialog")
                                    .find(".ui-dialog-titlebar") // the first button
                                    //.removeClass("ui-dialog-titlebar-close") // the first button
                                    .addClass(class_name);
                        }
                    }
            );

        }, ajaxAutocomplete: function (app_name, com_name, subset_name, ajax_name, task_name, data_object, html_field) {

            kazist_autocomplete.ajaxAutocomplete(app_name, com_name, subset_name, ajax_name, task_name, data_object, html_field);

        }, addSpinningIcon: function (this_element, type) {
            console.log(this_element);
            this_element.prepend('<i class="fa fa-spinner fa-spin"></i>');

        }, removeSpinningIcon: function (this_element) {

            this_element.find('.fa-spinner').remove();

        }, toggleNavbarMethod: function () {

            if (jQuery(window).width() > 768) {
                jQuery('.navbar .dropdown').on('mouseover', function () {
                    jQuery('.dropdown-toggle', this).trigger('click');
                }).on('mouseout', function () {
                    jQuery('.dropdown-toggle', this).trigger('click').blur();
                });
            } else {
                jQuery('.navbar .dropdown').off('mouseover').off('mouseout');
            }

        }, serializeFormJSON: function (html) {

            var o = {};
            var a = html.serializeArray();
            jQuery.each(a, function () {
                if (o[this.name]) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });

            return o;
        }, createNestedObject: function (obj, keys, v) {

            if (keys.length === 1) {
                obj[keys[0]] = v;
            } else {
                var key = keys.shift();
                obj[key] = kazist.createNestedObject(typeof obj[key] === 'undefined' ? {} : obj[key], keys, v);
            }

            return obj;

        }, createCookie: function (name, value, hours) {
            var expires;

            if (hours) {
                var date = new Date();
                date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            } else {
                expires = "";
            }

            document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";

        }, readCookie: function (name) {
            var nameEQ = encodeURIComponent(name) + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ')
                    c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0)
                    return decodeURIComponent(c.substring(nameEQ.length, c.length));
            }
            return null;


        }, eraseCookie: function (name) {
            kazist.createCookie(name, "", -1);
        }

    };
}();


kazist_ajax = function () {
    return {
        completed: false,
        callAjax: function (url, data_object, wait_to_complete) {
            var data = '';

            data_object['_token'] = jQuery('#_token').val();
            console.log(data_object);

            wait_to_complete = typeof wait_to_complete !== 'undefined' ? wait_to_complete : true;

            var ajax = jQuery.ajax({
                method: "POST",
                url: url,
                async: false,
                data: data_object
            }).done(function (msg) {
                if (kazist_ajax.isJsonString(msg)) {
                    data = jQuery.parseJSON(msg);
                } else {
                    data = msg;
                }
                //var json = $.parseJSON(data);
                kazist_ajax.completed = true;
            }).fail(function (jqXHR) {

                title = 'Ajax Call failed.';
                message = 'The Ajax call to the following url has failed.<br><br>' +
                        url + '<br><br>' +
                        'Status code is <b>' + jqXHR.status + '</b>';
                ;

                kazist.showDialog(title, message, 'error');
                kazist_ajax.completed = true;
            });

            if (wait_to_complete) {
                kazist_ajax.delayReturn();
            }

            return data;
        }, delayReturn: function () {

            console.log('test');
            if (!kazist_ajax.completed) {
                var delay = 1000;
                setTimeout(kazist_ajax.delayReturn(), delay);
            }

        }, isJsonString: function (str) {

            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }



    };
}();

