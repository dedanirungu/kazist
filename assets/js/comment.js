/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function () {

    var form_holder = jQuery('.kazi-comments > .kazi_comment_form_holder');

    kazi_comment.generateFormHTML(form_holder);
    kazi_comment.fetchComments();

});
kazi_comment = function () {
    return {
        saveComment: function (comment, comment_id, parent_id, attachments) {

            var data_object = {subset_id: kazist_document.extension_path, record_id: kazist_document.record_id, comment: comment, parent_id: parent_id, comment_id: comment_id, attachments: attachments};
            var msg = kazist.callAjaxByRoute('notification.comments.savecomment', data_object);
            kazi_comment.commentsHtml(msg);

        }, deleteComment: function (comment_id) {

            var data_object = {subset_id: kazist_document.extension_path, record_id: kazist_document.record_id, comment_id: comment_id};
            var msg = kazist.callAjaxByRoute('notification.comments.deletecomment', data_object);
            kazi_comment.commentsHtml(msg);

        },
        fetchComments: function () {

            var data_object = {subset_id: kazist_document.extension_path, record_id: kazist_document.record_id};
            var msg = kazist.callAjaxByRoute('notification.comments.fetchcomment', data_object);
            kazi_comment.commentsHtml(msg);
        },
        commentsHtml: function (msg) {

            var html = '';
            console.log(msg.successful);
            if (msg.successful) {

                html = kazi_comment.getCommentRecursivelyHtml(msg.comments, html);
                html = jQuery(html);
                html.find('.kazi_comment_form_holder').hide();

                html.find('.kazi_reply_comment_btn').on('click', function () {
                    var this_element = jQuery(this);
                    var single_comment = this_element.closest('.single-comment');
                    var form_holder = single_comment.find(' > .comment-wrapper >  .kazi_comment_form_holder');

                    kazi_comment.generateFormHTML(form_holder, true);
                });

                html.find('.kazi_edit_comment_btn').on('click', function () {
                    var this_element = jQuery(this);
                    var single_comment = this_element.closest('.single-comment');
                    var form_holder = single_comment.find('> .comment-wrapper > .kazi_comment_form_holder');
                    var comment = single_comment.find('> .comment-wrapper > #comment').val();

                    kazi_comment.generateFormHTML(form_holder, true, comment, true);
                });

                html.find('.kazi_delete_comment_btn').on('click', function () {
                    msg = 'Note: All Children Comment will be deleted together with this comment.' +
                            ' Are you sure you want to delete this comment?';

                    if (confirm(msg)) {

                        var this_element = jQuery(this);
                        var single_comment = this_element.closest('.single-comment');
                        var comment_id = single_comment.find('> .comment-wrapper > #comment_id').val();

                        kazi_comment.deleteComment(comment_id);
                    }

                });

                jQuery('.kazi-comments .kazi-comment-list').html(html);
            }

        },
        getCommentRecursivelyHtml: function (comments, html) {
            jQuery.each(comments, function (key, single_comment) {
                var attachments = single_comment.attachments;
                var new_html = '';

                html += '<div class="single-comment item">';
                html += '<img src="' + single_comment.avatar + '" align="left" alt="user image" class="online single-comment-image">';
               
                html += '<div class="comment-wrapper">';
                html += '<a href="#" class="name">'
                        + single_comment.user_full_name+'&nbsp;'
                        + ' <small class="text-muted"><i class="fa fa-clock-o"></i> ' + single_comment.date_created + '</small>'
                        + '</a>';

                html += '<p>';
                html += single_comment.comment;
                html += '</p>';
             

                if (attachments.length) {
                    html += '<div class="kazi_comment_attachments_holder attachment">';
                    jQuery.each(attachments, function (key, attachment) {

                        html += '<span>';
                        html += '<a href="' + kazi.url + '?app=media&com=media&view=edit&cid[]=' + attachment.id + '" target="_blank">';
                        html += '<img src="' + attachment.file_icon + '" width="24px" style="margin-left:10px"> &nbsp; ';
                        html += '</a>';
                        html += attachment.title;
                        html += '<input type="hidden" id="media_id" value="' + attachment.id + '">';
                        html += '<input type="hidden" id="media_title" value="' + attachment.title + '">';
                        html += '<input type="hidden" id="media_file_icon" value="' + attachment.file_icon + '">';
                        html += '</span>';
                    });
                    html += '</div>';
                }

                html += '<span class="kazi_comment_posting_info">';
                html += ' <a class="btn btn-default btn-xs kazi_reply_comment_btn"> Reply </a>';
                if (kazist_document.user_id === single_comment.created_by) {
                    html += ' <a class="btn btn-default btn-xs kazi_edit_comment_btn"> Edit </a>';
                    html += ' <a class="btn btn-default btn-xs kazi_delete_comment_btn"> Delete </a>';
                }
                html += '</span>';
                html += '</p>';
                html += '<div class="kazi_comment_form_holder attachment"></div>';

                if (single_comment.children.length) {
                    html += kazi_comment.getCommentRecursivelyHtml(single_comment.children, new_html);
                }

                html += '<input type="hidden" id="parent_id" value="' + single_comment.parent_id + '">';
                html += '<input type="hidden" id="comment_id" value="' + single_comment.id + '">';
                html += '<input type="hidden" id="comment" value="' + single_comment.comment + '">';

                html += '</div>';
                html += '</div>';
                html += '</div>';
            });

            return html;

        },
        generateFormHTML: function (host_object, host_is_hidden, comment, is_for_editing) {
            var html = '';

            var random_number = Math.ceil(Math.random(100, 10000) * 10000);

            if (comment === undefined) {
                comment = '';
            }
            var editing = (is_for_editing) ? 1 : 0;

            jQuery('.kazi-comment-list .kazi_comment_form_holder').hide();

            var tmp_html = '<div>';
            tmp_html += '<img src="' + kazist_document.web_root + '/' + comment_user_avatar + '">';

            tmp_html += '<div class="textarea-wrapper">';
            tmp_html += '<textarea id="kazi_reply_comments_post" class="comments" name="comments">' + comment + '</textarea>';

            tmp_html += '<div class="post-actions">';
            tmp_html += '<div class="post-by pull-left hidden-xs">';
            tmp_html += 'Post as: <span class="post-by-name">' + comment_user_name + '</span>';
            tmp_html += '</div>';
            tmp_html += '<button class="kazi_comment_save btn btn-success btn-sm pull-right" type="submit" name="reply_comment"><i class="fa fa-save"></i>Save</button>';
            tmp_html += '<a class="kazi_comment_attachment btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#mediaModal" field_name="' + random_number + '" application_name="notification" component_name="comments" subset_name="comments" saving_type="multiple" for_comment="1"><i class="fa fa-upload"></i> Attach </a> &nbsp;';
            tmp_html += '<input class="kazi_comment_editing" type="hidden" name="editing" value="' + editing + '">';
            tmp_html += '</div>';
            tmp_html += '</div>';

            tmp_html += '<div class="comment_attachment_' + random_number + '">';
            tmp_html += '<ul>';

            if (is_for_editing) {
                var attachments = host_object.closest('.single-comment').find('> .comment-wrapper > .kazi_comment_attachments_holder > span');

                attachments.each(function () {
                    attachment = jQuery(this);
                    var media_id = attachment.find('#media_id').val();
                    var media_title = attachment.find('#media_title').val();
                    var media_file_icon = attachment.find('#media_file_icon').val();
                    tmp_html += '<li style="margin-top:10px;">'
                            + '<a class="label label-danger delete_media"><span class="glyphicon glyphicon-trash"></span></a>'
                            + '&nbsp; <img src="' + media_file_icon + '" width="24px"> &nbsp; ' + media_title + ''
                            + '<input class="comment_attachment_ids" value="' + media_id + '" type="hidden">'
                            + '</li>';
                });
            }

            tmp_html += '</ul>';
            tmp_html += '</div>';

            tmp_html += '</div>';

            tmp_html = jQuery(tmp_html);

            tmp_html.find('.delete_media').on('click', function () {
                media_manager.deleteLiHtml(jQuery(this));
            });
            tmp_html.find('.kazi_comment_attachment').on('click', function () {
                var this_element = jQuery(this);
                media_manager.prepareDefaults(this_element);
            });

            tmp_html.find('.kazi_comment_save').on('click', function () {
                var attachments = Array();
                var parent_id = '';
                var comment_id = '';

                var comment = jQuery(this).closest('.kazi_comment_form_holder').find('.comments').val();
                var editing = jQuery(this).closest('.kazi_comment_form_holder').find('.kazi_comment_editing').val();
                var single_comment = jQuery(this).closest('.single-comment');
                var comment_attachment_ids = jQuery(this).closest('.attachment').find('.comment_attachment_ids');

                if (single_comment.length) {
                    parent_id = single_comment.find('> .comment-wrapper > #parent_id').val();
                    comment_id = single_comment.find('> .comment-wrapper > #comment_id').val();
                    if (!parseInt(editing)) {
                        parent_id = comment_id;
                        comment_id = '';
                    }
                }

                comment_attachment_ids.each(function () {
                    var attachment = jQuery(this).val();
                    attachments.push(attachment);
                });
                //console.log(comment_attachment_ids);
                if (comment !== '') {
                    kazi_comment.saveComment(comment, comment_id, parent_id, attachments);
                }

                jQuery(this).closest('.kazi_comment_form_holder').find('.comments').val('');
                jQuery(this).closest('.attachment').find('.comment_attachment_ids').closest('ul').remove();


                return false;
            });

            if (host_is_hidden) {
                host_object.show();
            }

            host_object.html(tmp_html);
        }
    };
}();
