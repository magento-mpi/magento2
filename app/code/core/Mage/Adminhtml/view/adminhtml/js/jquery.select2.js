/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
 (function ($, undefined) {
    "use strict";
    var treeToList = function(list, nodes, level) {
        $.each(nodes, function() {
            list.push({
                label: this.name,
                value: this.id,
                level: level,
                item: this
            });
            if ('children' in this) {
                treeToList(list, this.children, level + 1);
            }
        });
        return list;
    };
    $.fn.select2 = function (options) {
        this.each(function () {
            var $element = $(
                    '<div class="select2-container select2-container-multi">' +
                    '<ul class="select2-choices">' +
                    '<li class="select2-search-field">' +
                    '<input type="text" autocomplete="off" class="select2-input">' +
                    '</li></ul></div>'
                ),
                $list = $element.children(),
                $this = $(this),
                name = $this.attr('name'),
                $searchField = $list.find('.select2-search-field'),
                itemRenderer = function(value, text, data) {
                    $('<li class="select2-search-choice button"/>')
                        .data(data || {})
                        .append($('<input type="hidden" />').attr('name', name).val(value))
                        .append($('<div/>').text(text))
                        .append('<a href="#" onclick="return false;" ' +
                            'class="select2-search-choice-close" tabindex="-1"></a>'
                        )
                        .insertBefore($searchField);
                };
            $this.find('option').each(function(){
                itemRenderer($(this).val(), $(this).text());
            });
            $this.attr('disabled', 'disabled').hide();
            $this.data('select2-element', $element);
            $element.insertAfter($this);
            $list.delegate(".select2-search-choice-close", "click", function() {
                $(this).parent().remove();
            });
            $element.find('.select2-input').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: options.url,
                        dataType: "json",
                        data: {
                            name_part: request.term
                        },
                        success: function(data) {
                            response(treeToList([], data, 0));
                        }
                    });
                },
                minLength: 3,
                select: function(event, ui) {
                    var $el = $list.find('[name="product[category_ids][]"][value=' + parseInt(ui.item.value) + ']');
                    if ($el.length) {
                        return false;
                    }
                    itemRenderer(ui.item.value, ui.item.label, ui.item);
                    $element.find('.select2-input').val('');
                    return false;
                },
                focus: function() {
                    return false;
                }
            }).data("autocomplete")._renderItem = function(ul, item) {
                return $("<li>")
                    .data("item.autocomplete", item)
                    .append($("<a />")
                        .text(item.label)
                        .css({marginLeft: window.parseInt(item.level) * 16})
                    )
                    .appendTo(ul);
            };
        });
    };
})(jQuery);
