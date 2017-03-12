/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery('.timepicker').each(function () {
    jQuery(this).datetimepicker({
        datepicker: false,
        mask: true,
        format: 'H:i:s'
    });
});



jQuery('.datepicker').each(function () {
    jQuery(this).datetimepicker({
        timepicker: false,
        mask: true,
        format: 'Y-m-d'
    });
});


jQuery('.datetimepicker').each(function () {
    jQuery(this).datetimepicker({
        mask: true,
        format: 'Y-m-d H:i:s'
    });
});