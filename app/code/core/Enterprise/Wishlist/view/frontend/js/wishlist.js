/**
 * {license_notice}
 *
 * @category    EE frontend Wishlist
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true expr:true sub:true*/
/*global alert confirm*/
(function($) {
    'use strict';
    $.widget('mage.wishlist', {
        options: {
            createTmplSelector: '#create-tmpl',
            createTmplData: {
                btnCloseClass: 'btn-close',
                createWishlistBlockId: 'create-wishlist-block',
                createWishlistFormId: 'create-wishlist-form'
            },
            errorMsg: $.mage.__('Error happened while creating wishlist. Please try again later'),
            spinnerClass: 'loading'
        },

        _create: function() {
            this.element.on('click', '[data-wishlist-create]', $.proxy(function(e) {
                var json = $(e.target).data('wishlist-create'),
                    url = json['url'] ? json['url'] : this.options.createUrl,
                    isAjax = json['ajax'];
                this._showCreateWishlist(url, isAjax);
            }, this));
        },

        /**
         * Show create wishlist popup block
         * @private
         * @param url - create wishlist url
         * @param isAjax - if need to use ajax to call create wishlist url
         */
        _showCreateWishlist: function(url, isAjax) {
            this.createTmpl ? this.createTmpl.show() : this._initCreateTmpl();
            $('#' + this.options.createTmplData.createWishlistFormId).attr('action', url);
            this.createAjax = isAjax;
        },

        /**
         * Initialized jQuery template for create wishlist popup block, attach to dom and validation widget to form
         * @private
         */
        _initCreateTmpl: function() {
            this.createTmpl = $(this.options.createTmplSelector).tmpl(this.options.createTmplData);
            this.createTmpl.on('click', '.' + this.options.createTmplData.btnCloseClass, $.proxy(function() {
                this.createTmpl.hide();
            }, this))
            .appendTo('body');
            $('#' + this.options.createTmplData.createWishlistFormId).validation({
                submitHandler: $.proxy(function(form) {
                    if (this.createAjax) {
                        this._createWishlistAjax(form);
                    }
                }, this)
            });
        },

        /**
         * Call create wishlist url using ajax, when response returns, call callback function
         * @private
         * @param form - create wishlist form
         */
        _createWishlistAjax: function(form) {
            var _form = $(form), _this = this;
            $.ajax({
                url: _form.attr('action'),
                type: 'post',
                cache: false,
                data: _form.serialize(),
                beforeSend: function() {
                    $('#' + _this.options.createTmplData.createWishlistBlockId).addClass(_this.options.spinnerClass);
                },
                success: function(response) {
                    if (typeof response.wishlist_id !== 'undefined') {
                        _this._callback && _this._callback(response.wishlist_id);
                    } else if (typeof response.redirect !== 'undefined') {
                        window.location.href(response.redirect);
                    } else {
                        alert(_this.options.errorMsg);
                    }
                }
            });
        }
    });

    // Extension for mage.wishlist - Move to Wishlist
    $.widget('mage.wishlist', $.mage.wishlist, {
        options: {
            wishlistFormSelector: '#wishlist-view-form',
            formTmplSelector: '#form-tmpl',
            formTmplId: '#wishlist-hidden-form'
        },

        _create: function() {
            this._super();
            this.moveWishlistJson = this.element.find('[data-wishlist-move]').data('wishlist-move');
            this.element.on('click', '[data-wishlist-move-selected]', $.proxy(this._moveSelectedTo, this));
            this.element.on('click', '[data-wishlist-move-item]', $.proxy(this._moveItemTo, this));
        },

        /**
         * Move one wishlist item to another wishlist
         * @private
         * @param e - Item in wishlist got clicked
         */
        _moveItemTo: function(e) {
            var json = $(e.target).data('wishlist-move-item'),
                tmplJson = {
                    qty: this._getQty($(e.target)),
                    itemId: json['itemId'],
                    url: this.moveWishlistJson.moveItemUrl
                };
            if (json['new']) {
                this._moveItemToNew(tmplJson);
            } else {
                tmplJson.wishlistId = json['wishlistId'];
                $(this.options.formTmplSelector).tmpl(tmplJson).appendTo('body');
                $(this.options.formTmplId).submit();
            }
        },

        /**
         * Get wishlist item qty
         * @private
         * @param elem
         * @return {int} - Item qty
         */
        _getQty: function(elem) {
            var qty = elem.closest('tr').find('input.qty');
            return qty.length ? qty[0].value : null;
        },

        /**
         * Move selected wishlist items to another wishlist
         * @param e - move to wishlist button
         * @private
         */
        _moveSelectedTo: function(e) {
            var json = $(e.target).data('wishlist-move-selected'),
                wishlistId = json['wishlistId'];
            if (!this._itemsSelected()) {
                alert($.mage.__('You must select items to move'));
                return;
            }
            if (json['new']) {
                this._moveSelectedToNew();
            } else {
                var url = this.moveWishlistJson.moveSelectedUrl;
                url = url.replace("%wishlist_id%", wishlistId);
                $(this.options.wishlistFormSelector).attr('action', url).submit();
            }
        },

        /**
         * Move selected wishlist items to a new wishlist: involve show create wishlist popup,
         * using ajax to get new wishlistId, and passing wishlistId to _callback, which submits to moveSelectedUrl
         * @private
         * @param url - target url(can be move or copy)
         */
        _moveSelectedToNew: function(url) {
            this._callback = function(wishlistId) {
                var _url = url || this.moveWishlistJson.moveSelectedUrl;
                _url = _url.replace("%wishlist_id%", wishlistId);
                $(this.options.wishlistFormSelector).attr('action', _url).submit();
            };
            this._showCreateWishlist(this.options.createUrl, true);
        },

        /**
         * Move one wishlist item to a new wishlist: involve show create wishlist popup,
         * using ajax to get new wishlistId, and passing wishlistId to _callback, which submits to moveItemUrl
         * @private
         * @param tmplJson - a closure variable holds itemId, qty, and url
         */
        _moveItemToNew: function(tmplJson) {
            this._callback = function(wishlistId) {
                tmplJson.wishlistId = wishlistId;
                $(this.options.formTmplSelector).tmpl(tmplJson).appendTo('body');
                $(this.options.formTmplId).submit();
            };
            this._showCreateWishlist(this.options.createUrl, true);
        },

        /**
         * Make sure at lease one item is selected
         * @private
         * @return {Boolean}
         */
        _itemsSelected: function() {
            var selected = false;
            $(this.options.wishlistFormSelector).find('input.select').each(function() {
                if ($(this).is(':checked')) {
                    selected = true;
                    return;
                }
            });
            return selected;
        }
    });

    // Extension for mage.wishlist - Copy to Wishlist
    $.widget('mage.wishlist', $.mage.wishlist, {

        _create: function() {
            this._super();
            this.copyWishlistJson = this.element.find('[data-wishlist-copy]').data('wishlist-copy');
            this.element.on('click', '[data-wishlist-copy-selected]', $.proxy(this._copySelectedTo, this));
            this.element.on('click', '[data-wishlist-copy-item]', $.proxy(this._copyItemTo, this));
        },

        /**
         * Copy one wishlist item to a different wishlist
         * @private
         * @param e - Item in wishlist got clicked
         */
        _copyItemTo: function(e) {
            var json = $(e.target).data('wishlist-copy-item'),
                tmplJson = {
                    qty: this._getQty($(e.target)),
                    itemId: json['itemId'],
                    url: this.copyWishlistJson.copyItemUrl
                };
            if (json['new']) {
                this._copyItemToNew(tmplJson);
            } else {
                tmplJson.wishlistId = json['wishlistId'];
                $(this.options.formTmplSelector).tmpl(tmplJson).appendTo('body');
                $(this.options.formTmplId).submit();
            }
        },

        /**
         * Copy selected wishlist items to a different wishlist
         * @private
         * @param e - copy to wishlist button
         */
        _copySelectedTo: function(e) {
            var json = $(e.target).data('wishlist-copy-selected'),
                wishlistId = json['wishlistId'];
            if (!this._itemsSelected()) {
                alert($.mage.__('You must select items to copy'));
                return;
            }
            if (json['new']) {
                this._copySelectedToNew();
            } else {
                var url = this.copyWishlistJson.copySelectedUrl;
                url = url.replace("%wishlist_id%", wishlistId);
                $(this.options.wishlistFormSelector).attr('action', url).submit();
            }
        },

        /**
         * Copy selected wishlist items to a new wishlist
         * @private
         */
        _copySelectedToNew: function() {
            this._moveSelectedToNew(this.copyWishlistJson.copySelectedUrl);
        },

        /**
         * Copy one wishlist item to a new wishlist
         * @private
         * @param tmplJson - a closure variable holds itemId, qty, and url
         */
        _copyItemToNew: function(tmplJson) {
            this._moveItemToNew(tmplJson);
        }
    });

    // Extension for mage.wishlist - Delete Wishlist
    $.widget('mage.wishlist', $.mage.wishlist, {
        options: {
            delMsg: $.mage.__('You are about to delete your wish list.\nThis action cannot be undone.\nDo you want to proceed?')
        },

        _create: function() {
            this._super();
            this.element.on('click', '[data-wishlist-delete]', $.proxy(this._delWishlist, this));
        },

        /**
         * Delete wishlist and redirect to first wishlist
         * @private
         * @param e - Delete wishlist button
         */
        _delWishlist: function(e)  {
            if (confirm(this.options.delMsg)) {
                var json = $(e.target).data('wishlist-delete'),
                    wishlistId = json['wishlistId'],
                    delUrl = json['delUrl'].replace('%item%', wishlistId),
                    redirectUrl = json['redirectUrl'];
                $.ajax({
                    url: delUrl,
                    type: 'post',
                    cache: false,
                    success: function() {
                        window.location.href = redirectUrl;
                    }
                });
            }
        }
    });
})(jQuery);