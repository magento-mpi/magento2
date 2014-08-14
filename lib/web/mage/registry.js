/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([], function(){
    
    var events = (function(){
        var handlers = {
                uid: 0
            },
            elements = {};

        function clear( id ){
            var i,
                deps,
                index,
                pending;

            deps = handlers[id].deps;
            i = deps.length;

            while( i-- ){
                pending = elements[ deps[i] ];

                index = pending.indexOf( id );

                pending.splice( index, 1 );
                deps.splice( i, 1 );
            }

            delete handlers[ id ];
        }

        return {
            create: function( elems, callback ){
                var i   = elems.length,
                    uid = handlers.uid,
                    elem;

                while( i-- ){
                    elem = elems[i];

                    if( !elements[elem] ){
                        elements[elem] = [];
                    }
                    
                    elements[ elem ].push( uid );             
                }

                handlers[ uid ] = {
                    callback: callback,
                    deps: elems
                };

                handlers.uid++;
            },

            trigger: function( elem ){
                var i,
                    id,
                    handler,
                    records,
                    callbacks;

                callbacks = elements[ elem ];
                i = callbacks.length;

                while( i-- ){
                    id      = callbacks[ i ];
                    handler = handlers[ id ];

                    records = storage.getValues( handler.deps );

                    if( records.allSet ){
                        handler.callback.apply( window, records.values );

                        clear( id );
                    }
                }
            }
        };
    })();

    var storage = (function(){
        var data = {};

        return {
            getRecord: function( key ){
                if( !data[ key ] ){  
                    data[ key ] = {
                        hasValue: false
                    };
                }

                return data[ key ];
            },

            getValues: function( keys ){
                var i,
                    length,
                    record,
                    result;

                length = keys.length;

                result = {
                    values: [],
                    allSet: !!length
                };

                for( i = 0; i < length; i++ ){
                    record = this.getRecord( keys[i] );
                    
                    if( !record.hasValue ){
                        result.allSet = false;
                    }

                    result.values.push( record.value );
                }

                return result;
            },

            setValue: function( key, value ){
                var record = this.getRecord( key );
            
                record.value = value;
                record.hasValue = true;
            }
        };
    })();

    var proxy = (function(){
        function set( key, value ){
            storage.setValue( key, value );
            events.trigger( key );
        }

        function toArray( arr ){
            return typeof arr === 'string' ? arr.split(' ') : arr;
        }

        return {
            set: function( keys, value ){
                var i,
                    key;

                keys = toArray( keys );

                if( Array.isArray( keys ) ){
                    i = keys.length;

                    while( i-- ){
                        set(keys[i], value);
                    }
                }
                else{
                    for( key in keys ){
                        set(key, keys[key]);
                    }
                }
            },

            get: function( keys, fn ){
                var records;

                keys = toArray( keys );
                
                records = storage.getValues( keys );

                if( fn ){
                    records.allSet ?
                        fn.apply( this, records.values ) :
                        events.create( keys, fn );
                }

                return records.values.length > 1 ?
                    records.values :
                    records.values[0];
            },

            has: function( key ){
                return storage.getRecord( key ).hasValue;
            }
        };
    })();

    return proxy;
});

