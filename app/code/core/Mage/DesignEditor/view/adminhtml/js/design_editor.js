/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {

    /**
     * Widget block
     */
    $.widget( "vde.block", { _create: function() {}} );

    /**
     * Widget panel
     */
    $.widget('vde.vde_panel', {
        options: {
            cellSelector: '.vde_toolbar_cell',
            handlesHierarchySelector: '#vde_handles_hierarchy',
            treeSelector: '#vde_handles_tree'
        },
        _create: function() {
            this._initCells();
        },
        _initCells : function() {
            var self = this;
            this.element.find( this.options.cellSelector ).each( function(){
                $( this ).is( self.options.handlesHierarchySelector ) ?
                    $( this ).vde_menu( {treeSelector : self.options.treeSelector, slimScroll:true } ) :
                    $( this ).vde_menu();
            });
            this.element.find( this.options.cellSelector ).vde_menu();
        },
        _destroy: function() {
            this.element.find( this.options.cellSelector ).each( function(i, element) {
                $(element).data('vde_menu').destroy();
            });
            this._super();
        }
    });

    /**
     * Widget page
     */
    $.widget('vde.vde_page', {
        options: {
            frameSelector: 'iframe#vde_container_frame',
            containerSelector: '.vde_element_wrapper.vde_container',
            panelSelector: '#vde_toolbar',
            highlightElementSelector: '.vde_element_wrapper',
            highlightElementTitleSelector: '.vde_element_title',
            highlightCheckboxSelector: '#vde_highlighting',
            cookieHighlightingName: 'vde_highlighting',
            historyToolbarSelector: '.vde_history_toolbar',
            baseUrl: null,
            compactLogUrl: null,
            viewLayoutUrl: null
        },
        editorFrame: null,
        _create: function () {
            var self = this;
            $(this.options.frameSelector).load(function() {
                self.editorFrame = $(this).contents();
                self._initPanel();
            });
        },
        _initPanel: function () {
            $(this.options.panelSelector).vde_panel();
        }
    });

    /**
     * Widget page highlight functionality
     */
    var pageBasePrototype = $.vde.vde_page.prototype;
    $.widget('vde.vde_page', $.extend({}, pageBasePrototype, {
        _create: function () {
            pageBasePrototype._create.apply(this, arguments);
            if (this.options.highlightElementSelector) {
                this._initHighlighting();
                this._bind();
            }
        },
        _bind: function () {
            var self = this;
            this.element
                .on('checked.vde_checkbox', function () {
                    self._highlight();
                })
                .on('unchecked.vde_checkbox', function () {
                    self._unhighlight();
                });
        },
        _initHighlighting: function () {
            if (this.options.highlightCheckboxSelector) {
                $(this.options.highlightCheckboxSelector)
                    .vde_checkbox();
            }
            this.highlightBlocks = {};
        },
        _highlight: function () {
            var self = this;
            this.editorFrame.find(this.options.highlightElementSelector).each(function () {
                $(this)
                    .append(self._getChildren($(this).attr('id')))
                    .show()
                    .children(self.options.highlightElementTitleSelector).slideDown('fast');
            });
            this.highlightBlocks = {};
        },
        _unhighlight: function () {
            var self = this;
            this.editorFrame.find(this.options.highlightElementSelector).each(function () {
                var elem = $(this);
                elem.children(self.options.highlightElementTitleSelector).slideUp('fast', function () {
                    var children = elem.contents(':not(' + self.options.highlightElementTitleSelector + ')');
                    var parentId = elem.attr('id');
                    children.each(function () {
                        self._storeChild(parentId, this);
                    });
                    elem.after(children).hide();
                });
            });
        },
        _storeChild: function(parentId, child) {
            if (!this.highlightBlocks[parentId]) {
                this.highlightBlocks[parentId] = [];
            }
            this.highlightBlocks[parentId].push(child);
        },
        _getChildren: function(parentId) {
            return (!this.highlightBlocks[parentId]) ? [] : this.highlightBlocks[parentId];
        }
    }));

})( jQuery );
