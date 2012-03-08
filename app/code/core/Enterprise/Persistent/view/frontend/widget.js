/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!window.Enterprise) {
    window.Enterprise = {};
}

Enterprise.Widget = Class.create({
    _node: null,
    _children: [],

    initialize: function (node) {
        this._node = node;
    },

    getNode: function() {
        return this._node;
    },

    /**
     * @param {Enterprise.Widget} widget
     */
    addChild: function(widget) {
        this._children.push(widget);
        var children = $(this._node).immediateDescendants(),
            exists = false;
        $(this._node).immediateDescendants().each(function(child) {
            if (child == widget.getNode()) {
                exists = true;
            }
        });
        if (!exists) {
            widget.placeAt(this._node);
        }
    },

    placeAt: function(node) {
        $(node).insert(this._node);
    }
});

Enterprise.Widget.Dialog = Class.create(Enterprise.Widget, {

    _title: '',

    _titleNode: {},

    _contentNode: {},

    _backNode: {},

    _isPlaced: false,

    initialize: function ($super, title, content, additionalClass) {
        this._title = title;
        //this._node = new Element('div', {'class': 'popup-block block', 'style': {'display': 'none'}});
        this._node = new Element('div', {'class': 'popup-block block'});
        this._node.addClassName(additionalClass);
        //this._windowOverlay = new Element('div', {'class': 'window-overlay', 'style': {'display': 'none'}});
        this._windowOverlay = new Element('div', {'class': 'window-overlay'});
        var headerNode = new Element('div', {'class': 'block-title'});
        this._titleNode = new Element('strong').update(title);
        this._closeButton = new Element('div', {'class': 'btn-close'}).update('Close');
        $(this._closeButton).onclick = (function() {
            this.hide();
        }).bind(this);
        headerNode.insert(this._titleNode);
        headerNode.insert(this._closeButton);
        this._node.insert(headerNode);

        this._contentNode = new Element('div', {'class': 'block-content'});
        this._contentNode.insert(content);

        this._node.insert(this._contentNode);
    },

    place: function() {
        $(document.body).insert(this._windowOverlay);
        $(document.body).insert(this._node);
        this._isPlaced = true;
    },

    setTitle: function(title) {
        $(this._titleNode).update(title);
    },

    setContent: function(content) {
        $(this._contentNode).update(content);
    },

    getContent: function() {
        return this._contentNode;
    },

    show: function() {
        if (!this._isPlaced) {
            this.place();
        }
        //$(this._windowOverlay).setStyle({'display':'block'});
        $(this._windowOverlay).addClassName('active');
        this._windowOverlay.style.height=$$('body')[0].getHeight()+'px';
        //$(this._node).setStyle({'display': 'block'});
        $(this._node).addClassName('active');
    },

    hide: function() {
        //$(this._windowOverlay).setStyle({'display':'none'});
        $(this._windowOverlay).removeClassName('active');
        //$(this._node).setStyle({'display':'none'});
        $(this._node).removeClassName('active');
    },

    setBusy: function(state) {
        if (state) {
            $(this._node).addClassName('loading');
        } else {
            $(this._node).removeClassName('loading');
        }
    },

    destroy: function() {
        $(this._node).remove();
    }
});

Enterprise.Widget.SplitButton = Class.create(Enterprise.Widget, {
    _list: null,
    _templateString: '<strong><span></span></strong>' +
        '<a href="#" class="change"></a>' +
        '<div class="list-container">' +
            '<ul>' +
            '</ul>' +
        '</div>',

    initialize: function($super, title, alt, type) {
        if (typeof title != 'string') {
            $super(title);
        } else {
            $super(new Element('div', {'class': 'split-button split-button-created' + ((type)? ' ' + type: '')}));
            this._node.update(this._templateString);
            this._node.down('strong span').update(title);
            this._node.down('.change').update(alt);
        }
        Event.observe($(this._node).down('strong'), 'click', (function(event){this.onClick(event);}).bind(this));

        this._node.down('.change').setAttribute('tabindex', 20);
        this._list = $(this._node).down('ul');
        Event.observe($(this._node).down('.change'), 'click', this.onToggle.bind(this));
        Event.observe($(this._node).down('.change'), 'blur', this.close.bind(this));
    },

    onClick: function(event) {
    },

    onToggle: function(event) {
        Event.stop(event);
        if (this.isOpened()) {
            this.close();
        } else {
            this.open();
        }
    },

    isOpened: function() {
        return $(this._node).hasClassName('active');
    },

    open: function() {
        $(this._node).addClassName('active');
        this.onOpen();
    },

    onOpen: function() {
    },

    close: function() {
        $(this._node).removeClassName.bind($(this._node), 'active').delay(0.2);
        this.onClose();
    },

    onClose: function() {
    },

    /**
     * @param {Enterprise.Widget.SplitButton.Option} option
     */
    addOption: function(option) {
        option.placeAt(this._list);
        option.onClick = option.onClick.wrap((function(proceed) {
            proceed();
            this.close();
        }).bind(this));
    }
});

Enterprise.Widget.SplitButton.Option = Class.create(Enterprise.Widget, {

    initialize: function($super, title, type) {
        $super(new Element('li', {'class' : type ? type : null}));
        this._node.update('<span title="' + title + '">' + title + '</span>');
        Event.observe(this._node, 'click', (function(){this.onClick()}).bind(this));
    },

    getNode: function() {
        return this._node;
    },

    onClick: function() {

    }
});

Enterprise.loadSplitButtons = function() {
    if (typeof Enterprise.splitButtonsLoaded == 'undefined') {
        Enterprise.splitButtonsLoaded = true;
        $$('.split-button').each(function(node) {
            if (!$(node).hasClassName('split-button-created')) {
                new Enterprise.Widget.SplitButton(node);
            }
        });
    }
};

Enterprise.textOverflow = function(elem) {
    var container = $(elem);
    if (container.getStyle('overflow') == 'hidden') {
        var inner = container.down(0);
        var initialHeight = container.getHeight();
        if (inner.getHeight() > initialHeight) {
            var words = inner.innerHTML.split(' ');
            var test = new Element('span', {'style': 'visibility:hidden;'});
            test.style.width = container.getWidth();
            container.insert(test);
            var tempString = '';
            for (var i = 0; $(test).getHeight() <= initialHeight || i < words.legth; i++) {
                tempString = tempString + words[i] + ' ';
                test.update(tempString)
            };
            var finalstring = (words.slice(-words.length, i - 2)).join(' ');
            test.remove();
            inner.update(finalstring + '&hellip;');
        }
    }
};
Event.observe(document, 'dom:loaded', Enterprise.loadSplitButtons);
