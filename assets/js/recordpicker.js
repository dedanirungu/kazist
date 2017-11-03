/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function () {

    var querystring = 'twig_file=recordpicker';

    jQuery('a').each(function () {
        var href = $(this).attr('href');

        if (href && href !== '#' && href !== 'javascript:void') {
            href += (href.match(/\?/) ? '&' : '?') + querystring;
            jQuery(this).attr('href', href);
        }
    });

    jQuery('.insert-recordpicker').click(function () {
        var this_element = jQuery(this);

        var field_name = window.parent.recordpicker_fieldname;
        var displayfields = window.parent.recordpicker_displayfields;
        console.log(window.parent);

        var recordpicker_text = this_element.attr('recordpicker_text');
        var recordpicker_value = this_element.attr('recordpicker_value');

        //alert(recordpicker_text);

        window.parent.jQuery('.' + field_name + '_recordpicker .recordpicker_text').html(recordpicker_text);
        window.parent.jQuery('.' + field_name + '_recordpicker .recordpicker_text').val(recordpicker_text);
        window.parent.jQuery('.' + field_name + '_recordpicker .recordpicker_value').val(recordpicker_value);


        window.parent.jQuery('#recordpicker' + field_name + 'Modal').modal('hide');
    });
});