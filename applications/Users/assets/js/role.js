/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function () {

    role_view.addEvents(jQuery('body'));
});

role_view = function () {
    return {
        addEvents: function (html) {
            html.find('.role-application-right').on('click', function () {
                role_view.fetchApplicationRoles(jQuery(this));
            });

            html.find('.check_all').on('click', function () {
                role_view.addSelectAll(jQuery(this));
            });

            html.find('.uncheck_all').on('click', function () {
                role_view.clearSelection(jQuery(this));
            });

            html.find('.toggle_all').on('click', function () {
                role_view.toggleSelection(jQuery(this));
            });

        }, fetchApplicationRoles: function (this_element) {

            var application_id = this_element.attr('application_id');
            var role_id = this_element.attr('role_id');
            var content_container = this_element.closest('.panel').find('.panel-body');
            var content_table = this_element.closest('.panel').find('.panel-body table');

            kazist.addSpinningIcon(content_container);

            if (!content_table.length) {

                var data_object = {application_id: application_id, role_id: role_id};

                var form = kazist.callAjaxByRoute('users.roles.ajaxloadapplications', data_object, false);

                kazist.removeSpinningIcon(content_container);

                content_container.append(form);

                role_view.addEvents(content_container);
            }

        }, addSelectAll: function (this_element) {
            var application_name = this_element.attr('application_name')
            var selectedlist = jQuery('.' + application_name + '_td input');
            console.log(selectedlist);
            role_view.changeStatus(selectedlist, true);
        },
        toggleSelection: function (this_element) {
            var application_name = this_element.attr('application_name')
            var checked_selectedlist = jQuery('.' + application_name + '_td input:checked');
            var unchecked_selectedlist = jQuery('.' + application_name + '_td input');


            if (checked_selectedlist.length) {
                role_view.changeStatus(unchecked_selectedlist, true);
                role_view.changeStatus(checked_selectedlist, false);
            } else {
                role_view.changeStatus(unchecked_selectedlist, true);
            }

        },
        clearSelection: function (this_element) {
            var application_name = this_element.attr('application_name')
            var selectedlist = jQuery('.' + application_name + '_td input');
            role_view.changeStatus(selectedlist, false);
        },
        changeStatus: function (selectedlist, status) {
            selectedlist.each(function () {
                var selected = jQuery(this);
                var closest_div = selected.closest('div');

                selected.prop('checked', status);

                if (status) {
                    closest_div.addClass('checked');
                } else {
                    closest_div.removeClass('checked');
                }
            });
        }

    };
}();