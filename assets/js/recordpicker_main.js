/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var recordpicker_fieldname = '';
var recordpicker_displayfields = '';

jQuery(document).ready(function () {

    jQuery('.add-recordpicker').click(function () {

        var this_element = jQuery(this);
        var modal_id = this_element.data('target');
        var iframe_src = jQuery(modal_id).find('iframe').data('src');

        recordpicker_fieldname = this_element.attr('field_name');
        recordpicker_displayfields = this_element.attr('displayfields');
        jQuery('form').append('<input class="" type="hidden" name="twig_file" value="recordpicker">');

        jQuery(modal_id).find('iframe').attr('src', iframe_src);
    });
});