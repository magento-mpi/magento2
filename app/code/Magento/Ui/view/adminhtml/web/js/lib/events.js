/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
], function() {

   function addHandler( name, callback ){
        var events = this._events,
            event;

        event = events[name] = events[name] || [];

        event.push(callback);
    }

    function trigger( args, name ){
        var handlers;

        this._events = this._events || {};
        
        handlers = this._events[ name ];

        if( typeof handlers === 'undefined' ){
            return;
        }
        
        handlers.forEach( function( callback ){
            callback.apply( this, args );
        });
    }

    return {
        on: function( name, callback ){
            var key;

            this._events = this._events || {};

            if( typeof name === 'object' ){
                
                for(key in name){
                    addHandler.call( this, key, name[key] );
                }
            }
            else if( typeof callback === 'function' ){
                addHandler.call( this, name, callback );
            }

            return this;
        },

        off: function( name ){
            var handlers = this._events[name];

            if( Array.isArray(handlers) ){
                delete this._events[name];
            }

            return this;
        },

        trigger: function( events ){
            var args;

            args = Array.prototype.slice.call( arguments, 1 )

            if( typeof events === 'string' ){
                events = events.split(' ');
            }

            if( !Array.isArray(events) ){
                return this;
            }

            events.forEach( trigger.bind(this, args) );

            return this;
        }
    }
});