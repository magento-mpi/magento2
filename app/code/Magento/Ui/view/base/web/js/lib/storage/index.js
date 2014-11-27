/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Assembles storages returning storage mapping
 */
define([
    './storage',
    './meta',
    './dump'
], function(Storage, MetaStorage, DumpStorage){
    'use strict';

    return {
        meta:   MetaStorage,
        params: Storage,
        config: Storage,
        data:   Storage,
        dump:   DumpStorage
    }
});