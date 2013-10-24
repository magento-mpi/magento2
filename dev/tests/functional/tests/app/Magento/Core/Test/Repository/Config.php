<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Config Repository
 * Magento configuration settings
 *
 * @package Magento\Core\Test\Repository
 */
class Config extends AbstractRepository
{
    function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['default_tax_config'] = $this->_getDefaultTax();
        $this->_data['us_tax_config'] = $this->_getUsTax();
        $this->_data['display_price'] = $this->_getPriceDisplay();
        $this->_data['display_shopping_cart'] = $this->_getShoppingCartDisplay();
        $this->_data['paypal_express'] = $this->_getPaypalExpress();
        $this->_data['paypal_direct'] = $this->_getPaypalDirect();
        $this->_data['paypal_disabled_all_methods'] = $this->_getPaypalDisabled();
        $this->_data['paypal_payflow_pro'] = $this->_getPaypalPayFlowPro();
        $this->_data['authorizenet_disable'] = $this->_getAuthorizeNetDisable();
        $this->_data['authorizenet'] = $this->_getAuthorizeNet();
        $this->_data['flat_rate'] = $this->_getFlatRate();
        $this->_data['free_shipping'] = $this->_getFreeShipping();
    }

    protected function _getFreeShipping()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'carriers' => array(
                        'section' => 'carriers',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'freeshipping' => array( //Free Shipping
                                'fields' => array(
                                    'active' => array( //Enabled
                                        'value' => 1 //Yes
                                    ),
                                    'free_shipping_subtotal' => array( //Minimum Order Amount
                                        'value' => 10
                                    ),
                                    'sallowspecific' => array( //Ship to Applicable Countries
                                        'value' => 1 //Specific Countries
                                    ),
                                    'specificcountry' => array( //Ship to Applicable Countries
                                        'value' => 'US' //United States
                                    ),
                                    'showmethod' => array( //Show Method if Not Applicable
                                        'value' => 1 //Yes
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    protected function _getFlatRate()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'carriers' => array(
                        'section' => 'carriers',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'flatrate' => array( //Flat Rate
                                'fields' => array(
                                    'active' => array( //Enabled
                                        'value' => 1 //Yes
                                    ),
                                    'price' => array( //Price
                                        'value' => 5
                                    ),
                                    'type' => array( //Type
                                        'value' => 'I' //Per Item
                                    ),
                                    'sallowspecific' => array( //Ship to Applicable Countries
                                        'value' => 0 //All Allowed Countries
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    protected function _getAuthorizeNet()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'payment' => array(
                        'section' => 'payment',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'authorizenet' => array( //Credit Card (Authorize.net)
                                'fields' => array(
                                    'active' => array( //Enabled
                                        'value' => 1 //Yes
                                    ),
                                    'login' => array( //API Login ID
                                        'value' => '36sCtGS8w'
                                    ),
                                    'payment_action' => array( //Payment Action
                                        'value' => 'authorize'
                                    ),
                                    'trans_key' => array( //Transaction Key
                                        'value' => '67RY59y59p25JQsZ'
                                    ),
                                    'cgi_url' => array( //Gateway URL
                                        'value' => 'https://test.authorize.net/gateway/transact.dll'
                                    ),
                                    'test' => array( //Test Mode
                                        'value' => 0 //No
                                    ),
                                    'cctypes' => array( //Card Types
                                        'value' => 'AE,VI,MC,DI' //American Express, Visa, MasterCard, Discover
                                    ),
                                    'usecvv' => array( //Credit Card Verification
                                        'value' => 1 //Yes
                                    ),
                                    'centinel' => array( //3D Secure Card Validation
                                        'value' => 0 //No
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    protected function _getPaypalDisabled()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'payment' => array(
                        'section' => 'payment',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'paypal_express' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => 0
                                    )
                                )
                            ),
                            'paypal_standard' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => 0
                                    )
                                )
                            ),
                            'paypal_direct' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => 0
                                    )
                                )
                            ),
                            'verisign' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => 0
                                    )
                                )
                            ),
                            'paypaluk_express' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => 0
                                    )
                                )
                            ),
                            'payflow_advanced' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => 0
                                    )
                                )
                            ),
                            'payflow_link' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => 0
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    protected function _getPaypalDirect()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'payment' => array(
                        'section' => 'payment',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'paypal_group_all_in_one' => array( //PayPal All-in-One Payment Solutions
                                'groups' => array(
                                    'wpp_us' => array( //Payments Pro (Includes Express Checkout)
                                        'groups' => array(
                                            'wpp_required_settings' => array( //Required PayPal Settings
                                                'groups' => array(
                                                    'wpp_and_express_checkout' => array( //Payments Pro and Express Checkout
                                                        'fields' => array(
                                                            'business_account' => array( //Email Associated with PayPal
                                                                'value' => 'mtf_bussiness_pro@example.com'
                                                            ),
                                                            'api_authentication' => array( //API Authentication Methods
                                                                'value' => 0 //API Signature
                                                            ),
                                                            'api_username' => array( //API Username
                                                                'value' => 'mtf_bussiness_pro_api1.example.com'
                                                            ),
                                                            'api_password' => array( //API Password
                                                                'value' => '1380260177'
                                                            ),
                                                            'api_signature' => array( //API Signature
                                                                'value' => 'AEhCkH8sFI39Bz94iP79RT9Mt0MVAkCzF6NaWuXG2QtQFTkCUVG0z83m'
                                                            ),
                                                            'sandbox_flag' => array( //Sandbox Mode
                                                                'value' => 1 //Yes
                                                            ),
                                                            'use_proxy' => array( //API Uses Proxy
                                                                'value' => 0 //No
                                                            )
                                                        )
                                                    )
                                                ),
                                                'fields' => array(
                                                    'enable_wpp' => array( //Enable this Solution
                                                        'value' => 1 //Yes
                                                    )
                                                )
                                            ),
                                            'wpp_settings' => array( //Basic Settings - PayPal Payments Pro
                                                'fields' => array(
                                                    'payment_action' => array( //Payment Action
                                                        'value' => 'Authorization' //Authorization
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            ),
                            'paypal_express' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => 1
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    protected function _getPaypalExpress()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'payment' => array(
                        'section' => 'payment',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'paypal_alternative_payment_methods' => array( //PayPal Express Checkout
                                'groups' => array(
                                    'express_checkout_us' => array( //Express Checkout
                                        'groups' => array(
                                            'express_checkout_required' => array( //Required PayPal Settings
                                                'groups' => array(
                                                    'express_checkout_required_express_checkout' => array( //Express Checkout
                                                        'fields' => array(
                                                            'business_account' => array( //Email Associated with PayPal
                                                                'value' => 'mtf_bussiness_standard@example.com'
                                                            ),
                                                            'api_authentication' => array( //API Authentication Methods
                                                                'value' => 0 //API Signature
                                                            ),
                                                            'api_username' => array( //API Username
                                                                'value' => 'mtf_bussiness_standard_api1.example.com'
                                                            ),
                                                            'api_password' => array( //API Password
                                                                'value' => '1380260139'
                                                            ),
                                                            'api_signature' => array( //API Signature
                                                                'value' => 'AcYK4Hvrvg8x95.1ptMpawrNh0v4AdXYN21zLoEaQYfqqsD1du9YCpMB'
                                                            ),
                                                            'sandbox_flag' => array( //Sandbox Mode
                                                                'value' => 1 //Yes
                                                            ),
                                                            'use_proxy' => array( //API Uses Proxy
                                                                'value' => 0 //No
                                                            )
                                                        )
                                                    )
                                                ),
                                                'fields' => array(
                                                    'enable_express_checkout' => array( //Enable this Solution
                                                        'value' => 1 //Yes
                                                    )
                                                ),
                                            ),
                                            'settings_ec' => array( //Basic Settings - PayPal Payments Pro
                                                'fields' => array(
                                                    'payment_action' => array( //Payment Action
                                                        'value' => 'Authorization' //Authorization
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    protected function _getPaypalPayFlowPro() {
        return array(
            'data' => array(
                'sections' => array(
                    'payment' => array(
                        'section' => 'payment',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'paypal_payment_gateways' => array( // PayPal Payment Gateways
                                'groups' => array(
                                    'paypal_verisign_with_express_checkout_us' => array( // Payflow Pro (Includes Express Checkout)
                                        'groups' => array(
                                            'paypal_payflow_required' => array( // Required Paypal Settings
                                                'groups' => array(
                                                    'paypal_payflow_api_settings' => array( // Payflow Pro and Express Checkout
                                                        'fields' => array(
                                                            'using_pbridge' => array( // Use via Payment Bridge
                                                                'value' => 'No'
                                                            ),
                                                            'business_account' => array( // Email Associated with PayPal Merchant Account
                                                                'value' => 'pro_em_1350644409_biz@ebay.com'
                                                            ),
                                                            'partner' => array( // Partner
                                                                'value' => 'PayPal'
                                                            ),
                                                            'user' => array( // API User
                                                                'value' => 'petervarienpayflowpro'
                                                            ),
                                                            'vendor' => array( // Vendor
                                                                'value' => 'petervarienpayflowpro'
                                                            ),
                                                            'pwd' => array( // API Password
                                                                'value' => 'paypal123456'
                                                            ),
                                                            'sendbox_flag' => array( // Test Mode
                                                                'value' => 'Yes'
                                                            ),
                                                            'use_proxy' => array( // Use Proxy
                                                                'value' => 'No'
                                                            )
                                                        )
                                                    ),
                                                    'fields' => array(
                                                        'enable_paypal_payflow' => array( //Enable this Solution
                                                            'value' => 1 //Yes
                                                        )
                                                    )
                                                )
                                            ),
                                            'settings_paypal_payflow' => array( // Basic Settings - PayPal Payflow Pro
                                                'fields' => array(
                                                    'payment_action' => array( // Payment Action
                                                        'value' => 'Authorization'
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    protected function _getDefaultTax()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'tax' => array(
                        'section' => 'tax',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'calculation' => array(
                                'fields' => array(
                                    'algorithm' => array( //Tax Calculation Method Based On
                                        'value' => 'TOTAL_BASE_CALCULATION' //Total
                                    ),
                                    'based_on' => array( //Tax Calculation Based On
                                        'value' => 'shipping' //Shipping Address
                                    ),
                                    'price_includes_tax' => array( //Catalog Prices
                                        'value' => 0 //Excluding Tax
                                    ),
                                    'apply_after_discount' => array( //Apply Customer Tax
                                        'value' => 0 //Before Discount
                                    ),
                                    'discount_tax' => array( //Apply Discount On Prices
                                        'value' => 0 //Excluding Tax
                                    ),
                                    'apply_tax_on' => array( //Apply Tax On
                                        'value' => 0 //Custom Price if available
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    protected function _getPriceDisplay()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'tax' => array(
                        'section' => 'tax',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'display' => array( // Price Display Settings
                                'fields' => array(
                                    'type' => array( // Display Product Prices In Catalog
                                        'value' => 'Excluding Tax'
                                    ),
                                    'shipping' => array( // Display Shipping Prices
                                        'value' => 'Excluding Tax'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    protected function _getShoppingCartDisplay()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'tax' => array(
                        'section' => 'tax',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'cart_display' => array( // Shipping Cart Display Settings
                                'fields' => array(
                                    'price' => array( // Display Prices
                                        'value' => 'Excluding Tax'
                                    ),
                                    'subtotal' => array( // Display Subtotal
                                        'value' => 'Excluding Tax'
                                    ),
                                    'shipping' => array( // Display Shipping Amount
                                        'value' => 'Excluding Tax'
                                    ),
                                    'gift_wrapping' => array( // Display Gift Wrapping Prices
                                        'value' => 'Excluding Tax'
                                    ),
                                    'printed_card' => array( // Display Printed Card Prices
                                        'value' => 'Excluding Tax'
                                    ),
                                    'grandtotal' => array( // Include Tax In Grand Total
                                        'value' => 'No'
                                    ),
                                    'full_summary' => array( // Display Full Tax Summary
                                        'value' => 'No'
                                    ),
                                    'zero_tax' => array( // Display Zero Tax Subtotal
                                        'value' => 'No'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    /**
     * Provides taxes for US configuration based on default tax configuration
     *
     * @return array
     */
    protected function _getUsTax()
    {
        return $this->_getDefaultTax();
    }

    /**
     * Disable authorizenet payment
     *
     * @return array
     */
    protected function _getAuthorizeNetDisable()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'payment' => array(
                        'section' => 'payment',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'authorizenet' => array( //Credit Card (Authorize.net)
                                'fields' => array(
                                    'active' => array( //Enabled
                                        'value' => 0 //No
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }
}
