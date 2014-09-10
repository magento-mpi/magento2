define([
    './storage',
    './meta'
], function(Storage, MetaStorage){
    'use strict';

    return {
        meta:   MetaStorage,
        params: Storage,
        config: Storage,
        data:   Storage
    }
});