/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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