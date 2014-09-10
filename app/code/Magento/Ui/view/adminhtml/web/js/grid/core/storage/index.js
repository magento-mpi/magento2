/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './storage',
    './meta'
], function(Storage, MetaStorage){
    'use strict';

    return {
        meta:   MetaStorage,
        params: Storage,
        config: Storage,
        data:   Storage,
        dump:   Storage
    }
});