/**
 * {license_notice}
 *
 * @category    frontend poll
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($) {
    // Default value for menu
    var pollInit = {
        formId: '#pollForm',
        pollAnswersId: '#poll-answers'
    };

    $(document).ready(function () {
        // Trigger initalize event
        mage.event.trigger("mage.poll.initialize", pollInit);
        mage.decorator.list(pollInit.pollAnswersId);
        $(pollInit.formId).on('submit', function () {
            var options = $('input.poll_vote');
            for (i in options) {
                if (options[i].checked == true) {
                    return true;
                }
            }
            return false;
        });
    });
}(jQuery));