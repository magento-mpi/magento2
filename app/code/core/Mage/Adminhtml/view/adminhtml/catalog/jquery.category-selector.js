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
    var treeToList = function(list, nodes, level, path) {
        $.each(nodes, function() {
            list.push({
                label: this.name,
                value: this.id,
                level: level,
                item: this,
                path: path + this.name
            });
            if ('children' in this) {
                treeToList(list, this.children, level + 1, path + this.name + '/' );
            }
        });
        return list;
    };
    $.fn.categorySelector = function (options) {
        this.each(function () {
            var $element = $(
                    '<div class="category-selector-container category-selector-container-multi">' +
                    '<ul class="category-selector-choices">' +
                    '<li class="category-selector-search-field">' +
                    '<input type="text" autocomplete="off" data-ui-id="category-selector-input" class="category-selector-input">' +
                    '</li></ul></div>'
                ),
                $list = $element.children(),
                $this = $(this),
                name = $this.attr('name'),
                $searchField = $list.find('.category-selector-search-field'),
                itemRenderer = function(value, text, data) {
                    $('<li class="category-selector-search-choice button"/>')
                        .data(data || {})
                        .append($('<input type="hidden" />').attr('name', name).val(value))
                        .append($('<div/>').text(text))
                        .append('<a href="#" onclick="return false;" ' +
                            'class="category-selector-search-choice-close" tabindex="-1"></a>'
                        )
                        .insertBefore($searchField);
                };
            $element.append($('<input type="hidden" />').attr('name', name));
            $this.find('option').each(function(){
                itemRenderer($(this).val(), $(this).text());
            });
            $this.attr('disabled', 'disabled').hide();
            $this.data('category-selector-element', $element);
            $element.insertAfter($this);
            $list.delegate(".category-selector-search-choice-close", "click", function() {
                $(this).parent().remove();
            });
            $element.find('.category-selector-input').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: options.url,
                        dataType: "json",
                        data: {
                            name_part: request.term
                        },
                        success: function(data) {
                            response(treeToList([], data, 0, ''));
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
                    $element.find('.category-selector-input').val('');
                    return false;
                },
                focus: function() {
                    return false;
                }
            }).data("autocomplete")._renderItem = function(ul, item) {
                var level = window.parseInt(item.level);
                return $("<li>")
                    .data("item.autocomplete", item)
                    .append($("<a />", {
                            'data-level': level,
                            'data-ui-id': 'category-selector-' + item.value
                        })
                        .attr('title', item.path)
                        .addClass('level-' + level)
                        .text(item.label)
                        .css({marginLeft: level * 16})
                    )
                    .appendTo(ul);
            };
        });
    };
})(jQuery);
