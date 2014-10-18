/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    '../collapsible'
], function(_, Collapsible) {
    'use strict';

    var __super__ = Collapsible.prototype;

    return Collapsible.extend({
        initElement: function(elem){
            __super__.initElement.apply(this, arguments);
        },

        insert: function(elems, offset){
            __super__.insert.apply(this, arguments);

            console.log(elems);

            return this;
        },

        insertAt: function(offset, index, elem){
            var _elems = this._elems,
                el;

            _elems[index + offset] = elem;
                

            el = _.compact(_.flatten(_elems));
            console.log( el );

            //this.elems();
            //this.initElement(elem);
        },
    });
});