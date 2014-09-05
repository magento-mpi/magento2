define([
    './storage'
], function(storage) {
    'use strict';

    var id = 0,
        wait = {},
        map = {};

    function clear(resolved) {
        var ei,
            elems,
            index,
            pending;

        resolved.forEach(function(cid) {
            elems = wait[cid].deps;

            for (ei = elems.length; ei--;) {
                pending = map[elems[ei]];

                index = pending.indexOf(cid);

                if (~index) {
                    pending.splice(index, 1);
                }
            }

            delete wait[cid];
        });
    }

    return {
        resolve: function(elem) {
            var pending,
                handler,
                elems,
                resolved;

            pending = map[elem];

            if (typeof pending === 'undefined') {
                return;
            }

            resolved = [];

            pending.forEach(function(cid) {
                handler = wait[cid];
                elems = handler.deps;

                if (storage.has(elems)) {
                    handler.callback.apply(window, storage.get(elems));
                    resolved.push(cid);
                }
            });

            clear(resolved);
        },

        wait: function(elems, callback) {

            if (storage.has(elems)) {
                return callback.apply(window, storage.get(elems));
            }

            wait[id] = {
                callback: callback,
                deps: elems
            };

            elems.forEach(function(elem) {
                elem = map[elem] = map[elem] || [];

                elem.push(id);
            });

            id++;
        }
    };
});