/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
jQuery(document).ready(function(){
    jQuery('#system_messages').on('surveyYes surveyNo', function(e, data) {
        if (e.type == 'surveyYes') {
            var win = window.open(data.surveyUrl, '', 'width=900,height=600,resizable=1,scrollbars=1');
            win.focus();
        }
        jQuery.ajax({
            url: data.surveyAction,
            type: 'post',
            data: {decision: data.decision}
        })
    });

});
