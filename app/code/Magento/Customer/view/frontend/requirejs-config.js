/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

var config = {
    map: {
        '*': {
            checkoutBalance:    'Magento_Customer/js/checkout-balance',
            address:            'Magento_Customer/address',
            setPassword:        'Magento_Customer/set-password'
        }
    },
    deps: [
        'mage/validation/dob-rule'
    ]
};