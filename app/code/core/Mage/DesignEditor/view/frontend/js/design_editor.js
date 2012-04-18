/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($) {
    /**
     * Class for design editor
     */
    DesignEditor = function () {
        this._enableDragDrop();
    };

    DesignEditor.prototype._enableDragDrop = function () {
        var thisObj = this;
        /* Enable reordering of draggable children within their containers */
        $('.vde_element_wrapper.vde_container').sortable({
            items: '.vde_element_wrapper.vde_draggable',
            tolerance: 'pointer',
            revert: true,
            helper: 'clone',
            appendTo: 'body',
            placeholder: 'vde_placeholder',
            start: function(event, ui) {
                thisObj._resizePlaceholder(ui.placeholder, ui.item);
                thisObj._outlineDropContainer(this);
                /* Enable dropping of the elements outside of their containers */
                var otherContainers = $('.vde_element_wrapper.vde_container').not(ui.item);
                $(this).sortable('option', 'connectWith', otherContainers);
                otherContainers.sortable('refresh');
            },
            over: function(event, ui) {
                thisObj._outlineDropContainer(this);
            },
            stop: function(event, ui) {
                thisObj._removeDropContainerOutline();
            }
        }).disableSelection();
        return this;
    };

    DesignEditor.prototype._resizePlaceholder = function (placeholder, element) {
        placeholder.css({height: $(element).outerHeight(true) + 'px'});
    };

    DesignEditor.prototype._outlineDropContainer = function (container) {
        this._removeDropContainerOutline();
        $(container).addClass('vde_container_hover');
    };

    DesignEditor.prototype._removeDropContainerOutline = function () {
        $('.vde_container_hover').removeClass('vde_container_hover');
    };

    DesignEditor.prototype.highlight = function (isOn) {
        if (isOn) {
            this._turnHighlightingOn();
        } else {
            this._turnHighlightingOff();
        }
        return this;
    };

    DesignEditor.prototype._turnHighlightingOn = function () {
        $('.vde_element_wrapper').each(function () {
            var elem = $(this);
            var children = $('[vde_parent_element="' + elem.attr('id') + '"]');
            children.removeAttr('vde_parent_element');
            elem.show().append(children);
            elem.children('.vde_element_title').slideDown('fast');
        });
        return this;
    };

    DesignEditor.prototype._turnHighlightingOff = function () {
        $('.vde_element_wrapper').each(function () {
            var elem = $(this);
            elem.children('.vde_element_title').slideUp('fast', function () {
                var children = elem.children(':not(.vde_element_title)');
                children.attr('vde_parent_element', elem.attr('id'));
                elem.after(children).hide();
            });
        });
        return this;
    };
})(jQuery);
