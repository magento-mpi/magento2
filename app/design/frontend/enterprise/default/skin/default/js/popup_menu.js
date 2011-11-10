/**
 * DHTML popup-menu
 *
 * {license_notice}
 *
 * @category    design
 * @package     enterprise_default
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!window.Enterprise) {
    window.Enterprise = {};
}

Enterprise.PopUpMenu = {
    currentPopUp: null,
    documentHandlerInitialized: false,
    popUpZIndex: 994,
    hideDelay: 2000,
    hideOnClick: true,
    hideInterval: null,
    initializeDocumentHandler: function () {
        if (!this.documentHandlerInitialized) {
            this.documentHandlerInitialized = true;
            Event.observe(
                document.body,
                'click',
                this.handleDocumentClick.bindAsEventListener(this)
            );
        }
    },
    handleDocumentClick: function (evt) {
        if (this.currentPopUp !== null) {
            var element = Event.element(evt);
            if (!this.currentPopUp.onlyShowed && this.hideOnClick) {
                this.hide();
            } else {
                this.currentPopUp.onlyShowed = false;
            }
        }
    },
    handlePopUpOver: function (evt) {
        if (this.currentPopUp !== null) {
            this.currentPopUp.removeClassName('faded');
            this.resetTimeout(0);
        }
    },
    handlePopUpOut: function (evt) {
        if (this.currentPopUp !== null) {
            this.currentPopUp.addClassName('faded');
            this.resetTimeout(1);
        }
    },
    show: function (trigger) {
        this.initializeDocumentHandler();

        var container = $(trigger).up('.switch-wrapper');
        if (!$('popId-' + container.id)) {
            return;
        }

        if (this.currentPopUp !== null && $('popId-' + container.id) !== this.currentPopUp) {
            this.hide(true);
        } else if (this.currentPopUp !== null && this.currentPopUp === $('popId-' + container.id)) {
            this.hide();
            return;
        }

        this.currentPopUp = $('popId-' + container.id);
        this.currentPopUp.container = container;
        this.currentPopUp.container.oldZIndex = this.currentPopUp.container.style.zIndex;
        this.currentPopUp.container.style.zIndex = this.popUpZIndex;
        new Effect.Appear(this.currentPopUp, { duration:0.3 });


        if (!this.currentPopUp.isHandled) {
            this.currentPopUp.observe('mouseover', this.handlePopUpOver.bindAsEventListener(this));
            this.currentPopUp.observe('mouseout', this.handlePopUpOut.bindAsEventListener(this));
            this.currentPopUp.isHandled = true;
        }
        this.currentPopUp.onlyShowed = true;
        this.currentPopUp.container.down('.switcher').addClassName('list-opened');
        this.resetTimeout(2);
    },
    hide: function () {
        if (this.currentPopUp !== null) {
            if (arguments.length == 0) {
                new Effect.Fade(this.currentPopUp, {duration: 0.3});
            } else {
                this.currentPopUp.hide();
            }
            this.currentPopUp.container.style.zIndex = this.currentPopUp.container.oldZIndex;
            this.resetTimeout(0);
            this.currentPopUp.container.down('.switcher').removeClassName('list-opened');
            this.currentPopUp = null;
        }
    },
    resetTimeout: function (delay) {
        if (this.hideTimeout !== null) {
            clearTimeout(this.hideTimeout);
            this.hideTimeout = null;
        }
        if (delay) {
            this.hideTimeout = setTimeout(
                this.hide.bind(this),
                this.hideDelay * delay
            );
        }
    }
};


function popUpMenu(element) {
   Enterprise.PopUpMenu.show(element);
}
