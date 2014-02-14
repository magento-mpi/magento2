/**
 * {license_notice}
 *
 * @category    mage compare list
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global confirm:true*/
(function ($) {
    $.widget('mage.dataPost', {
        options: {
            formTemplate: '<form action="${action}" method="post">{{each data}}<input name="${$index}" value="${$value}">{{/each}}</form>',
            postTrigger: ['a[data-post]', 'button[data-post]', 'span[data-post]']
        },
        _create: function() {
            this._bind();
        },
        _bind: function() {
            var events = {};
            $.each(this.options.postTrigger, function(index, value) {
                events['click ' + value] = '_postDataAction';
            });
            this._on(events);
        },
        _postDataAction: function(e) {
            e.preventDefault();
            this.postData($(e.currentTarget).data('post'));
        },
        postData: function(data) {
            $.tmpl(this.options.formTemplate, data).appendTo('body').hide().submit();
        }
    });
    $(document).dataPost();
})(jQuery);