define([
    './storage',
    './events'
], function( storage, events ){
    'use strict';
    
    function set( elem, value ){
        storage.set( elem, value );
        events.resolve( elem );
    }

    function stringToArray( st ){
        return typeof st === 'string' ? st.split(' ') : st; 
    }

    return {
        get: function( elems, callback ){
            elems = stringToArray( elems );

            return typeof callback !== 'undefined' ? 
                events.wait( elems, callback ) :
                storage.get( elems );
        },

        set: function( elems, value ){
            var i;

            elems = stringToArray( elems );

            if( !Array.isArray( elems ) ){
                for( i in elems ){
                    set( i, elems[i] );
                }
            }
            else{
                for( i = elems.length; i--; ){
                    set( elems[i], value )
                }
            }

            return this;
        },

        remove: function( elems ){
            storage.remove( stringToArray( elems ) );
        },

        has: function( elems ){
            return storage.has( stringToArray(elems) );
        }

    };
});