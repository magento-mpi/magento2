/**
 * {license_notice}
 *
 * @category    frontend poll
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global mage:true */
(function ($) {
    // Default fields to initialize for poll
    var pollInit = {
        formId: '#pollForm',
        pollAnswersId: '#poll-answers',
        pollCheckedOption: 'input.poll_vote:checked'
    };

    $(document).ready(function () {
        // Trigger initalize event
        mage.event.trigger("mage.poll.initialize", pollInit);
        mage.decorator.list(pollInit.pollAnswersId);
        $(pollInit.formId).on('submit', function () {
            return $(pollInit.pollCheckedOption).length > 0;
        });
    });
}(jQuery));