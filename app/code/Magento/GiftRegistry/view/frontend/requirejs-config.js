/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

var config = {
    map: {
        '*': {
            advancedSearch: 'Magento_GiftRegistry/advanced-search',
            giftRegistry:   'Magento_GiftRegistry/gift-registry',
            addressOption:  'Magento_GiftRegistry/address-option',
            validation:     'Magento_Catalog/product/view/validation' 
        }
    },
    deps: [
        'Magento_GiftRegistry/js/opcheckout'
    ]
};