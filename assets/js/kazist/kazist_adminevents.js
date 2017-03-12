/* 
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */


kazist_adminevents = function () {
    return {
        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx start yesNoStatusUpdate
        yesNoStatusUpdate: function (this_element) {

            var confirm_status = confirm("Are you sure you and to change Status");

            if (confirm_status) {
                kazist_adminevents.updateServer(this_element);
                kazist_adminevents.changeColor(this_element);
            }

        }, updateServer: function (this_element) {

            var form = this_element.closest('form');

            var url = kazist_document.web_base + '/' + kazist_document.document.route + '/task?activity=updatestatus';

            var item_id = this_element.attr('item_id');
            var item_status = this_element.attr('item_status');
            var item_field = this_element.attr('item_field');

            var data_object = {item_id: item_id, item_status: item_status, item_field: item_field};

            kazist.callAjax(url, data_object, true);

        }, changeColor: function (this_element) {

            if (this_element.hasClass('bg-green')) {

                this_element.removeClass('bg-green').addClass('bg-red');
                this_element.find('i').removeClass('fa-check').addClass('fa-times');

            } else {

                this_element.removeClass('bg-red').addClass('bg-green');
                this_element.find('i').removeClass('fa-times').addClass('fa-check');

            }

        }
        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx end yesNoStatusUpdate
    };
}();


