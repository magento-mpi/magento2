/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'm2/registry'
], function( registry ){


    /**
     * Initializes components assigned to a specied element via data-* attribute.
     * @param {HTMLElement} el - Element to initialize components with.
     * @private
     */
    function initElement( el ){
        var data,
            component,
            config;

        data = JSON.parse( el.dataset.m2Init );

        for( component in data ){

            if( registry.has(el, component) ){
                return;
            }

            config = getConfig( el, component, data[component] );

            require( [component], function( constr ){
                registry.add( el, component );

                constr( el, config );                
            });
        }
    }


    /**
     * Searches for a configurational object assigned to an element.
     * @param {HTMLElement} el - Element that will be used as a parentNode for a configurational element.
     * @param {String} componet - Components name.
     * @param {String|Object} config - Configurational object or its' selector.
     * @returns {Object} Components' configuration.
     * @private
     */
    function getConfig( el, component, config ){
        var configNode;

        if(typeof config !== 'object'){

            configNode = getConfigNode( config, component, el );

            if( configNode ){
                config = JSON.parse( configNode.firstChild.nodeValue );
            }
        }

        return config || {};
    }


    /**
     * Searches for a components' configurational node.
     * @param {string} selector - Configurational node selector.
     * @param {String} componet - Components name.
     * @param {HTMLElement} parent - Element that will used as a parentNode for a configurational
            element in case if its' selector is not specified.
     * @returns {HTMLElement} Configurational node.
     * @private
     */
    function getConfigNode( selector, component, parent ){
        var node;

        node = selector ?
            document.querySelector( selector.replace(/%/g, '"') ) :
            parent.querySelector( 'm2-config[data-role="'+ component +'"]' );

        if( !selector && (!node || node.parentNode !== parent) ){
            node = false;
        }

        return node;
    }

    return {
        /**
         * Initializes components assigned to HTML elements via [data-m2-init].
         */
        init: function(){
            var els = Array.prototype.slice.call( document.querySelectorAll('[data-m2-init]') );

            els.forEach( initElement );
        },

        /**
         * Creates a wrapper function on a jQuerys' component constructor.
         * @param {jQuery} $ - jQuery object.
         * @param {String} constr - Constructors' name.
         * returns {Function}
         */
        jqWrapper: function( $, constr ){
            return function( el, data ){
                return $.fn[constr].call( $(el), data );
            }
        }
    }
});