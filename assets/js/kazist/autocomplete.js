/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

kazist_autocomplete = function () {
    return {
        completed: false,
        ajaxAutocomplete: function (url, data_object, html_field) {

            var field_name = html_field.attr('auto_field');
            var fields = kazist.callAjax(url, data_object, false);
            
            html_field
                    .bind("keydown", function (event) {
                        
                        if (event.keyCode === jQuery.ui.keyCode.TAB &&
                                jQuery(this).data("ui-autocomplete").menu.active) {
                            event.preventDefault();
                        }
                    })
                    .autocomplete({
                        minLength: 3,
                        messages: {
                            noResults: '',
                            results: function () {
                            }
                        },
                        source: fields,
                        focus: function () {
                            // prevent value inserted on focus
                            return true;
                        },
                        select: function (event, ui) {

                            var terms = kazist_autocomplete.split(this.value);

                            jQuery("#" + field_name).val(ui.item.value);

                            jQuery(this).val(ui.item.label);

                            // remove the current input
                            //terms.pop();
                            // add the selected item
                            //  terms.push(ui.item.value);
                            // add placeholder to get the comma-and-space at the end
                            // terms.push("");
                            // this.value = terms.join(", ");
                            return false;
                        }
                    });

            for (var key in fields) {
                if (fields.hasOwnProperty(key)) {

                    //console.log(key + " -> " + fields[key]);
                    default_value = 0;
                    var checked_str = '';

                    if (fields[key] === default_value) {
                        checked_str = 'selected="selected"';
                    }
                    var html = '<option value="' + fields[key] + '" ' + checked_str + '>' + fields[key] + '</option>';
                    //console.log(html_field);
                    html_field.append(html);
                }
            }



        }, split: function (val) {
            return val.split(/,\s*/);
        },
        extractLast: function (term) {
            return kazist_autocomplete.split(term).pop();
        }
    };
}();



