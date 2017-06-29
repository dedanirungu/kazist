/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var private_field_name = '';

jQuery(document).ready(function () {

    rights_field.addEvents(jQuery('body'));
});

rights_field = function () {
    return {
        addEvents: function (html) {
            html.find('.private_confidential ins').on('click', function () {
                var this_element = jQuery(this);
                var div_element = jQuery(this).closest('div');
                var input_element = this_element.closest('div').find('input');

                private_field_name = input_element.attr('field_name');

                jQuery('.other_private_confidential').hide();
                jQuery('.other_private_confidential table td input').attr('name', '');
                jQuery('.all_access_rights').attr('name', 'form[' + private_field_name + ']');

                if (div_element.hasClass('checked')) {
                    jQuery('.other_private_confidential').show();
                    jQuery('.all_access_rights').attr('name', '');
                    jQuery('.other_private_confidential table td input').attr('name', 'form[' + private_field_name + '][]');
                }


            });
            html.find('.add_other_private_confidential').on('click', function () {
                private_field_name = jQuery(this).attr('field_name');
                rights_field.getUsersList(0);
            });

            html.find('.delete_private_confidential').on('click', function () {
                jQuery(this).closest('tr').remove();
                return false;
            });

            return html;

        }, getUsersList: function (offset) {
            var user_ids = new Array();
            var user_elements = jQuery('.private_confidential_' + private_field_name);

            user_elements.each(function () {
                var user_id = jQuery(this).val();
                user_ids.push(user_id);
            });


            jQuery.ajax({
                type: "POST",
                url: kazi.url,
                data: {app: "users", com: "users", controller: 'UsersController', task: 'userslist', user_ids: user_ids, offset: offset}
            }).done(function (msg) {
                msg = JSON.parse(msg);

                rights_field.updateUserToAdd(msg);

            });
        },
        fetchUser: function (subset_id, record_id) {
            jQuery.ajax({
                type: "POST",
                url: kazi.url,
                data: {app: "users", com: "users", controller: 'UsersController', task: 'fetchusers', subset_id: subset_id, record_id: record_id}
            }).done(function (msg) {
                msg = JSON.parse(msg);

                rights_field.updateUserHtml(msg);

            });
        },
        updateUserToAdd: function (msg) {
            var html = '';
            if (msg.successful) {
                html += '<ul>';
                jQuery.each(msg.records, function (key, record) {
                    html += '<li>';
                    html += '<img width="64px" src="' + record.media_file + '"><br>';
                    html += record.name + '<br>';
                    html += '<a class="btn btn-primary btn-xs insert-user" href="#" user_id="' + record.id + '" user_name="' + record.name + '">Add User</a>';
                    html += '</li>';
                });
                html += '<ul>';
                rights_field.setPaginationParams(msg);
            } else {
                html += 'No Users To Add.';
            }

            html = jQuery(html);

            html.find('.insert-user').on('click', function () {
                var html = '';
                var user_id = jQuery(this).attr('user_id');
                var user_name = jQuery(this).attr('user_name');

                html += '<tr>';
                html += '<td><b>' + user_name + '</b></td>';
                html += '<td class="text-right">';
                html += '<input class="private_confidential_' + private_field_name + '" type="hidden" name="form[' + private_field_name + '][]" value="' + user_id + '">';
                html += '<a class="delete_private_confidential" href="#">';
                html += '<span class="label label-danger">';
                html += '<span class="glyphicon glyphicon-trash">';
                html += '</span>';
                html += '</span>';
                html += '</a>';
                html += '</td>';
                html += '</tr>';

                html = rights_field.addEvents(jQuery(html));

                jQuery('.other_private_confidential table tbody tr.no_one_can_access').remove();
                jQuery('.other_private_confidential table tbody').append(html);
                jQuery(this).closest('li').hide('slow');
                //rights_field.saveUser(kazi.subset_id, user_id, kazi.record_id);
                return false;
            });


            jQuery('.right_field_modal .modal-body').html(html);

        }, setPaginationParams: function (users) {
            var current_page = 1;

            jQuery('#kazi-potential-users .previous').removeClass('disabled');
            jQuery('#kazi-potential-users .next').removeClass('disabled');

            if (users.offset == 0) {
                jQuery('#kazi-potential-users .previous').addClass('disabled').off();
            } else {
                jQuery('#kazi-potential-users .previous').off().on('click', function (e) {
                    e.stopPropagation();
                    offset = users.offset - users.limit;
                    rights_field.getUsersList(offset);
                });
            }

            if ((users.total - users.offset) < users.limit) {
                jQuery('#kazi-potential-users .next').addClass('disabled').off();
            } else {
                jQuery('#kazi-potential-users .next').off().on('click', function (e) {
                    e.stopPropagation();
                    offset = +users.offset + users.limit;
                    rights_field.getUsersList(offset);
                });
            }

            page_count = Math.ceil(users.total / users.limit);

            if (users.offset > 0) {
                current_page = Math.ceil(users.offset / users.limit) + 1;
            }

            if (page_count > 1) {
                jQuery('#kazi-potential-users .page_count').html('Page ' + current_page + ' of ' + page_count);
            }

        },
        updateUserHtml: function (msg) {
            var html = '';
            var btn_html = jQuery('.kazi-right_field .kazi-right_field-btn');

            btn_html.html(msg.user_text);

            if (msg.has_right) {
                btn_html.addClass('btn-danger').removeClass('btn-success');
            } else {
                btn_html.addClass('btn-success').removeClass('btn-danger');
            }

            if (msg.successful) {
                jQuery.each(msg.users, function (key, single_avatar) {
                    html += '<img src="' + single_avatar.media_file + '">';
                });

                jQuery('.kazi-right_field .avatar').html(html);
            } else {
                jQuery('.kazi-right_field .avatar').html(' No Users.');
            }

            return html;
        }
    };
}();
