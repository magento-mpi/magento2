/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Assembles storages for form provider
 */
define([
    'Magento_Ui/js/lib/storage/storage'
], function(Storage){
    'use strict';

    return {
        meta:   Storage,
        params: Storage,
        data:   Storage,
        dump:   Storage
    }
});