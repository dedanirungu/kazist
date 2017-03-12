/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// grab your file object from a file input

var kazi_media_fieldformname = '';
var kazi_media_fieldname = '';
var kazi_media_appname = '';
var kazi_media_comname = '';
var kazi_media_subsetname = '';
var kazi_media_saving_type = '';
var kazi_media_for_comment = '';

jQuery(document).ready(function () {

    jQuery('.delete_media').click(function (e) {
        media_manager.deleteTrHtml(jQuery(this));
    });

    jQuery('.kazi-medias-server-link').click(function (e) {
        media_manager.getMediaList();
    });

    jQuery('#mediaModal').on('hide.bs.modal', function (e) {
        jQuery('.kazi-medias-progress').html('');
    });

    jQuery('.kazi_media_upload').click(function () {
        var this_element = jQuery(this);
        jQuery('#mediaModal .kazicode_media_source').val('media');
        media_manager.prepareDefaults(this_element);
    });

    jQuery('#kazicode_media_search_keyword').blur(function () {
        media_manager.getMediaList();
    });

    jQuery('#kazicode_media_search_type').change(function () {
        media_manager.getMediaList();
    });

    jQuery('#kazicode_media_search_error').change(function () {
        media_manager.getMediaList();
    });

    jQuery('#kazicode_media').change(function () {
        this_element = jQuery(this);
        //sendFile(this.files[0]);
        jQuery.each(this.files, function (key, file) {
            media_manager.sendFile(this_element, key, file, kazi_media_fieldname, kazi_media_fieldformname);
        });
    });

    jQuery('#dropzone').on(
            'dragover',
            function (e) {
                e.preventDefault();
                e.stopPropagation();
            }
    )
    jQuery('#dropzone').on(
            'dragenter',
            function (e) {
                e.preventDefault();
                e.stopPropagation();
            }
    )

    jQuery('#dropzone').on(
            'drop',
            function (e) {
                if (e.originalEvent.dataTransfer) {
                    if (e.originalEvent.dataTransfer.files.length) {
                        this_element = jQuery(this);
                        e.preventDefault();
                        e.stopPropagation();

                        /*UPLOAD FILES HERE*/
                        file_list = e.originalEvent.dataTransfer.files;

                        jQuery.each(file_list, function (key, file) {
                            media_manager.sendFile(this_element, key, file, kazi_media_fieldname, kazi_media_fieldformname);
                        });
                    }
                }
            }
    );

});
media_manager = function () {
    return {
        prepareDefaults: function (this_element) {

            kazi_media_fieldformname = this_element.attr('field_form_name');
            kazi_media_fieldname = this_element.attr('field_name');
            kazi_media_appname = this_element.attr('application_name');
            kazi_media_comname = this_element.attr('component_name');
            kazi_media_subsetname = this_element.attr('subset_name');
            kazi_media_saving_type = this_element.attr('saving_type');
            kazi_media_for_comment = this_element.attr('for_comment');
            // alert(kazi_media_for_comment);

        }, sendFile: function (this_element, key, file, fieldname, fieldformname) {
            html = jQuery('.kazi-media-progress-default').html();
            html = '<div class="kazi-media-progress_' + key + '">' + html + '</div>';

            jQuery('.kazi-medias-progress').append(html);
            jQuery('.kazi-media-progress_' + key + ' .kazi-media-name').html(file.name);

            var data = new FormData();

            data.append('media', file);
            //  data.append('title', this_element.closest('.modal-body').find('#kazicode_media_title').val());
            // data.append('root_route', kazist_document.document.root_route);

            var title = this_element.closest('.modal-body').find('#kazicode_media_title').val();
            var route = kazist_document.document.root_route;

            console.log(kazist_document.document.root_route);

            data.append('_token', jQuery('#_token').val());

            jQuery.ajax({
                type: 'POST',
                url: kazist_document.document.web_base + '/media/media/upload?name=' + file.name + '&title=' + title + '&root_route=' + route,
                data: data,
                mimeType: "multipart/form-data",
                cache: false,
                processData: false, // Don't process the files
                dataType: "json",
                contentType: false,
                success: function () {
                    // do something
                },
                xhrFields: {
                    // add listener to XMLHTTPRequest object directly for progress (jquery doesn't have this yet)
                    onprogress: function (progress) {
                        // calculate upload progress
                        var percentage = Math.floor((progress.total / progress.totalSize) * 100);
                        jQuery('.kazi-media-progress_' + key + ' .kazi-media-size').html(progress.total + 'KB');
                        jQuery('.kazi-media-progress_' + key + ' .progress-bar')
                                .width(percentage + '%');
                        // log upload progress to console
                        if (percentage === 100) {
                            jQuery('.kazi-media-progress_' + key + ' .progress-bar')
                                    .width('100%')
                                    .removeClass('progress-bar-red')
                                    .addClass('progress-bar-green');
                        }
                    }
                }
            }).done(function (medias) {
                console.log(medias);
                jQuery('.kazi-media-progress_' + key + ' .progress-bar')
                        .width('100%')
                        .removeClass('progress-bar-red')
                        .addClass('progress-bar-green');



                // medias = jQuery.parseJSON(medias);
                jQuery.each(medias.records, function (key_new, record) {
                    if (kazi_media_saving_type == 'json' || kazi_media_saving_type == 'multiple') {
                        media_manager.getTrHtml(record, fieldname);
                    } else {
                        media_manager.getImageHtml(record, fieldname);
                    }
                });
            });

        }, getMediaList: function (offset) {

            var inputs = new Array;
            var data = new FormData();

            var search_keyword = jQuery('#kazicode_media_search_keyword').val();
            var search_type = jQuery('#kazicode_media_search_type').val();
            var search_error = jQuery('#kazicode_media_search_error').val();
            var search_source = jQuery('#kazicode_media_source').val();

            data.append('keyword', search_keyword);
            data.append('type', search_type);
            data.append('error', search_error);
            data.append('source', search_source);

            jQuery('.' + kazi_media_fieldname + "_medias_list #" + kazi_media_fieldname + "_medias_value").each(function () {
                //inputs.push(jQuery(this).val());
                data.append('ids[]', jQuery(this).val());
            });

            jQuery('.kazi-medias-server').html('');
            jQuery('.kazi-medialist-progress .progress-bar').width('20%');
            jQuery('.kazi-medialist-progress').show();

            if (offset) {
                data.append('offset', offset);
            }
            
            data.append('_token', jQuery('#_token').val());

            jQuery.ajax({
                type: 'POST',
                url: kazist_document.document.web_base + '/media/media/listing',
                data: data,
                cache: false,
                processData: false, // Don't process the files
                contentType: false,
                success: function () {
                    // do something
                }
            }).done(function (medias) {

                jQuery('.kazi-medialist-progress').show();
                jQuery('.kazi-medialist-progress .progress-bar').width('60%');

                //console.log(medias);
                medias = jQuery.parseJSON(medias);

                var html = '<ul>';
                jQuery.each(medias.records, function (key_new, record) {
                    html += media_manager.getServerLiHtml(record);
                });
                html += '</ul>';
                html += '<div class="clr"></div>';

                html = jQuery(html);
                html.find('.inset_media_record').on('click', function () {
                    var this_element = jQuery(this);
                    media_manager.prepareDocument4Insert(this_element);
                });

                media_manager.setPaginationParams(medias);

                jQuery('.kazi-medias-server').html(html);

                jQuery('.kazi-medialist-progress .progress-bar').width('100%');
                jQuery('.kazi-medialist-progress').hide();

            });

        }, setPaginationParams: function (medias) {
            var current_page = 1;

            jQuery('#kazi-medias-server-wrapper .previous').removeClass('disabled');
            jQuery('#kazi-medias-server-wrapper .next').removeClass('disabled');

            if (medias.offset === 0) {
                jQuery('#kazi-medias-server-wrapper .previous').addClass('disabled').off();
            } else {
                jQuery('#kazi-medias-server-wrapper .previous').off().on('click', function (e) {
                    e.stopPropagation();
                    offset = medias.offset - medias.limit;
                    media_manager.getMediaList(offset);
                });
            }

            if ((medias.total - medias.offset) < medias.limit) {
                jQuery('#kazi-medias-server-wrapper .next').addClass('disabled').off();
            } else {
                jQuery('#kazi-medias-server-wrapper .next').off().on('click', function (e) {
                    e.stopPropagation();
                    offset = +medias.offset + +medias.limit;
                    media_manager.getMediaList(offset);
                });
            }

            page_count = Math.ceil(medias.total / medias.limit);

            if (medias.offset > 0) {
                current_page = Math.ceil(medias.offset / medias.limit) + 1;
            }

            if (page_count > 1) {
                jQuery('#kazi-medias-server-wrapper .page_count').html('Page ' + current_page + ' of ' + page_count);
            }

        }, prepareDocument4Insert: function (this_element) {

            var record = new Object();
            var media_source = jQuery('#mediaModal .kazicode_media_source').val();
            var input_id = jQuery('#mediaModal .kazicode_media_input_id').val();

            record.id = this_element.closest('li').find('.media_id').val();
            record.title = this_element.closest('li').find('.media_title').val();
            record.image = this_element.closest('li').find('.media_image').val();
            //this_element.closest('li').remove();

            this_element.closest('li').hide('slow', function () {
                jQuery(this).remove();
            });

            if (media_source == 'media') {
                if (parseInt(kazi_media_for_comment)) {
                    media_manager.getCommentImageHtml(record, kazi_media_fieldname);
                } else if (kazi_media_saving_type == 'json' || kazi_media_saving_type == 'multiple') {
                    media_manager.getTrHtml(record, kazi_media_fieldname);
                } else {
                    media_manager.getImageHtml(record, kazi_media_fieldname);
                }
            } else {
                var image_url = record.image;
                var new_image_url = image_url.replace('../', '');

                jQuery('#' + input_id).val(new_image_url);
                jQuery('#mediaModal').modal('hide');
            }

        }, getCommentImageHtml: function (record, fieldformname) {
            // console.log(fieldformname);
            var html = '';

            html += '<li style="margin-top:10px;">';
            html += '<a class="label label-danger delete_media"><span class="glyphicon glyphicon-trash"></span></a>';
            html += '&nbsp; <img width="32px" src="' + kazist_document.document.web_root + '/' + record.image + '"/>';
            html += '&nbsp; ' + record.title;
            html += '<input class="comment_attachment_ids"  type="hidden" value="' + record.id + '"/>';
            html += '</li>';

            html = jQuery(html);

            html.find('.delete_media').click(function () {
                media_manager.deleteLiHtml(jQuery(this));
            });

            jQuery('.comment_attachment_' + fieldformname + ' ul').append(html);

        }, getImageHtml: function (record, fieldformname) {
            //console.log(fieldformname);
            var html = '';
            html += '<img src="' + kazist_document.document.web_root + '/' + record.image + '"/> ';
            html += '<input id="' + fieldformname + '_medias_value" name="form[' + fieldformname + ']" type="hidden" value="' + record.id + '"/>';

            jQuery('.' + fieldformname + '_medias_single').html(html);

        }, getTrHtml: function (record, fieldformname) {
            //console.log(fieldformname);
            var html = '';
            html += '<tr>';
            html += '<td>';
            html += '<img width="32px" src="' + kazist_document.document.web_root + '/' + record.image + '" class="tooltip_element" data-placement="bottom" data-toggle="tooltip" title="' + record.title + '"/>';
            html += '</td>';
            html += '<td>';
            html += record.title;
            html += '<input id="' + fieldformname + '_medias_value" name="form[' + fieldformname + '][]" type="hidden" value="' + record.id + '"/>';
            html += '</td>';
            html += '<td>';
            html += '<a class="label label-danger delete_media"><span class="glyphicon glyphicon-trash"></span></a>';
            html += '</td>';
            html += '</tr>';

            html = jQuery(html);

            html.find('.delete_media').click(function () {
                media_manager.deleteTrHtml(jQuery(this));
            });

            jQuery('.' + fieldformname + '_medias_list').append(html);

        }, deleteTrHtml: function (this_element) {
            var can_delete = confirm('Are you sure you want to delete?');

            if (can_delete) {
                this_element.closest('tr').remove();
            }
        }, deleteLiHtml: function (this_element) {
            var can_delete = confirm('Are you sure you want to delete?');

            if (can_delete) {
                this_element.closest('li').remove();
            }

        }, getServerLiHtml: function (record) {

            var html = '';
            html += '<li>';
            html += '<img width="64px" height="64px" src="' + kazist_document.document.web_root + '/' + record.image + '" class="tooltip_element" data-placement="bottom" data-toggle="tooltip" title="' + record.title + '"/>';
            html += '<br>';
            html += record.title;
            html += '<input class="media_id" type="hidden" name="" value="' + record.id + '">';
            html += '<input class="media_title" type="hidden" name="" value="' + record.title + '">';
            html += '<input class="media_image" type="hidden" name="" value="' + record.image + '">';
            html += '<br>';
            html += '<br>';
            html += '<a href="#" class="inset_media_record"><span class="label label-info"><span class="glyphicon glyphicon-pencil"> Insert</span></span></a>';
            html += '</li>';
            return html;

        }
    };
}();


