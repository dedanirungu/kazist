/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// grab your file object from a file input

jQuery(document).ready(function() {
    
    jQuery('.multiple_add_field').click(function() {
        var this_element = jQuery(this);
        var this_container = this_element.closest('.kazicode-form-field');
        var html = this_container.find('.duplicate_this_field').html();

        html = jQuery(html);

        html.find('.multiple_remove_field').on('click', function() {
            return removeMultipleField(jQuery(this));
        });

        this_container.find('.kazicode-form-field-items').append(html);

        return false;
    });
    
    jQuery('.multiple_remove_field').click(function() {
        return removeMultipleField(jQuery(this));
    });
    
});

function removeMultipleField(this_element) {
    var this_container = this_element.closest('.kazicode-form-field-item');
    this_container.remove();
    return false;
}

