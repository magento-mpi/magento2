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
    './storage'
], function(Storage){
    'use strict';

    return {
        meta:   Storage,
        params: Storage,
        data:   Storage,
        dump:   Storage
    }
});