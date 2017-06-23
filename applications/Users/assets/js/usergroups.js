/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function () {

    rights_edit_view.addEvents(jQuery('body'));
});

rights_edit_view = function () {
    return {
        addEvents: function (html) {
            html.find('.user_rights .check_all').on('click', function () {
                rights_edit_view.addSelectAll(jQuery(this));
            });

            html.find('.user_rights .uncheck_all').on('click', function () {
                rights_edit_view.clearSelection(jQuery(this));
            });

            html.find('.user_rights .toggle_all').on('click', function () {
                rights_edit_view.toggleSelection(jQuery(this));
            });

        },
        addSelectAll: function (this_element) {
            var application_name = this_element.attr('application_name')
            var selectedlist = jQuery('.' + application_name + '_td input');
            console.log(selectedlist);
            rights_edit_view.changeStatus(selectedlist, true);
        },
        toggleSelection: function (this_element) {
            var application_name = this_element.attr('application_name')
            var checked_selectedlist = jQuery('.' + application_name + '_td input:checked');
            var unchecked_selectedlist = jQuery('.' + application_name + '_td input');


            if (checked_selectedlist.length) {
                rights_edit_view.changeStatus(unchecked_selectedlist, true);
                rights_edit_view.changeStatus(checked_selectedlist, false);
            } else {
                rights_edit_view.changeStatus(unchecked_selectedlist, true);
            }

        },
        clearSelection: function (this_element) {
            var application_name = this_element.attr('application_name')
            var selectedlist = jQuery('.' + application_name + '_td input');
            rights_edit_view.changeStatus(selectedlist, false);
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