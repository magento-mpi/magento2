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
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );
        //Tax
        $this->_data['default_tax_config'] = $this->_getDefaultTax();
        $this->_data['us_tax_config'] = $this->_getUsTax();
        $this->_data['display_price'] = $this->_getPriceDisplay();
        $this->_data['display_shopping_cart'] = $this->_getShoppingCartDisplay();
        //Payment methods
        $this->_data['paypal_express'] = $this->_getPaypalExpress();
        $this->_data['paypal_direct'] = $this->_getPaypalDirect();
        $this->_data['paypal_disabled_all_methods'] = $this->_getPaypalDisabled();
        $this->_data['paypal_payflow_pro'] = $this->_getPaypalPayFlowPro();
        $this->_data['paypal_payflow_pro_3d_secure'] = $this->_getPayPalPayflowPro3dSecure();
        $this->_data['paypal_payments_pro_3d_secure'] = $this->_getPayPalPaymentsPro3dSecure();
        $this->_data['authorizenet_disable'] = $this->_getAuthorizeNetDisable();
        $this->_data['authorizenet'] = $this->_getAuthorizeNet();
        $this->_data['authorizenet_3d_secure'] = $this->_getAuthorizeNet3dSecure();
        $this->_data['paypal_payflow'] = $this->_getPayPalPayflow();
        //Payment Services
        $this->_data['3d_secure_credit_card_validation'] = $this->_get3dSecureCreditCardValidation();
        //Shipping methods
        $this->_data['flat_rate'] = $this->_getFlatRate();
        $this->_data['free_shipping'] = $this->_getFreeShipping();
        //Catalog
        $this->_data['enable_mysql_search'] = $this->_getMysqlSearchEnabled();
        $this->_data['check_money_order'] = $this->getCheckmo();
        $this->_data['general_store_information'] = $this->getGeneralStore();
        $this->_data['customer_vat'] = $this->getCustomerVat();
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
                                        'value' => '"67RY59y59p25JQsZ"'
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
                                    'useccv' => array( //Credit Card Verification
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

    protected function _getAuthorizeNet3dSecure()
    {
        $data = array(
            'data' => array(
                'sections' => array(
                    'payment' => array(
                        'section' => 'payment',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'authorizenet' => array( //Credit Card (Authorize.net)
                                'fields' => array(
                                    'centinel' => array( //3D Secure Card Validation
                                        'value' => 1 //No
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
        return array_merge_recursive($data, $this->_getAuthorizeNet());
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
                                                ),
                                                'groups' => array(
                                                    'wpp_settings_advanced' => array(
                                                        'fields' => array(
                                                            'centinel' => array( //3D Secure Card Validation
                                                                'value' => 0
                                                            )
                                                        )
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
                                                                'value' => 'paymentspro@biz.com'
                                                            ),
                                                            'api_authentication' => array( //API Authentication Methods
                                                                'value' => 0 //API Signature
                                                            ),
                                                            'api_username' => array( //API Username
                                                                'value' => 'paymentspro_api1.biz.com'
                                                            ),
                                                            'api_password' => array( //API Password
                                                                'value' => '1369911703'
                                                            ),
                                                            'api_signature' => array( //API Signature
                                                                'value' => 'AOolWQExAt2k.RZzqZ6i6hWlSW4vAnkvVXvL8r1P-kXgqaV7sfD.ftNQ'
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
                                                ),
                                                'groups' => array(
                                                    'settings_ec_advanced' => array(
                                                        'fields' => array(
                                                            'debug' => array(
                                                                'value' => 0
                                                            )
                                                        ),
                                                    )
                                                ),
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

    /**
     * Get Configuration Settings for PayPal Payflow Pro Payment Method
     *
     * @return array
     */
    protected function _getPaypalPayFlowPro()
    {
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
                                                            'business_account' => array( // Email Associated with PayPal Merchant Account
                                                                'value' => 'pro_em_1350644409_biz@ebay.com'
                                                            ),
                                                            'partner' => array( // Partner
                                                                'value' => 'PayPal'
                                                            ),
                                                            'user' => array( // API User
                                                                'value' => 'empayflowpro'
                                                            ),
                                                            'vendor' => array( // Vendor
                                                                'value' => 'empayflowpro'
                                                            ),
                                                            'pwd' => array( // API Password
                                                                'value' => 'Temp1234'
                                                            ),
                                                            'sandbox_flag' => array( // Test Mode
                                                                'value' => 1
                                                            ),
                                                            'use_proxy' => array( // Use Proxy
                                                                'value' => 0
                                                            )
                                                        )
                                                    )
                                                ),
                                                'fields' => array(
                                                    'enable_paypal_payflow' => array( //Enable this Solution
                                                        'value' => 1 //Yes
                                                    )
                                                )
                                            ),
                                            'settings_paypal_payflow' => array( // Basic Settings - PayPal Payflow Pro
                                                'fields' => array(
                                                    'payment_action' => array( // Payment Action
                                                        'value' => 'Authorization'
                                                    )
                                                ),
                                                'groups' => array(
                                                    'settings_paypal_payflow_advanced' => array(
                                                        'fields' => array(
                                                            'centinel' => array( //3D Secure Card Validation
                                                                'value' => 0
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            ),
                            'paypaluk_express' => array(
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

    /**
     * Data for PayPal Payflow Pro Edition method with 3D Secure
     */
    protected function _getPayPalPayflowPro3dSecure()
    {
        $data =  array(
            'data' => array(
                'sections' => array(
                    'payment' => array(
                        'section' => 'payment',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'paypal_payment_gateways' => array(
                                'groups' => array(
                                    'paypal_verisign_with_express_checkout_us' => array(
                                        'groups' => array(
                                            'settings_paypal_payflow' => array(
                                                'groups' => array(
                                                    'settings_paypal_payflow_advanced' => array(
                                                        'fields' => array(
                                                            'centinel' => array( //3D Secure Card Validation
                                                                'value' => 1
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
                )
            )
        );
        return array_merge_recursive($data, $this->_getPaypalPayFlowPro());
    }

    /**
     * Provide Configuration for Default Tax settings
     *
     * @return array
     */
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

    /**
     * Provides Price Display Configuration
     *
     * @return array
     */
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
                                        'value' => 1 //Excluding Tax
                                    ),
                                    'shipping' => array( // Display Shipping Prices
                                        'value' => 1 //Excluding Tax
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
     * Provides Shopping Cart Display Configuration
     *
     * @return array
     */
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
                                        'value' => 1,
                                    ),
                                    'subtotal' => array( // Display Subtotal
                                        'value' => 1,
                                    ),
                                    'shipping' => array( // Display Shipping Amount
                                        'value' => 1,
                                    ),
                                    'gift_wrapping' => array( // Display Gift Wrapping Prices
                                        'value' => 1,
                                    ),
                                    'printed_card' => array( // Display Printed Card Prices
                                        'value' => 1,
                                    ),
                                    'grandtotal' => array( // Include Tax In Grand Total
                                        'value' => 0,
                                    ),
                                    'full_summary' => array( // Display Full Tax Summary
                                        'value' => 0,
                                    ),
                                    'zero_tax' => array( // Display Zero Tax Subtotal
                                        'value' => 0,
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
     * Data for PayPal Payflow Edition method
     */
    protected function _getPayPalPayflow()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'payment' => array(
                        'section' => 'payment',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'paypal_payment_gateways' => array(
                                'groups' => array(
                                    'paypal_verisign_with_express_checkout_us' => array(
                                        'groups' => array(
                                            'paypal_payflow_required' => array(
                                                'groups' => array(
                                                    'paypal_payflow_api_settings' => array(
                                                        'fields' => array(
                                                            'business_account' => array(
                                                                'value' => 'pro_em_1350644409_biz@ebay.com'
                                                            ),
                                                            'partner' => array(
                                                                'value' => 'PayPal'
                                                            ),
                                                            'user' => array(
                                                                'value' => 'empayflowpro'
                                                            ),
                                                            'vendor' => array(
                                                                'value' => 'empayflowpro'
                                                            ),
                                                            'pwd' => array(
                                                                'value' => 'Temp1234'
                                                            ),
                                                            'sandbox_flag' => array(
                                                                'value' => 1
                                                            ),
                                                            'enable_paypal_payflow' => array(
                                                                'value' => 1
                                                            ),
                                                            'use_proxy' => array(
                                                                'value' => 0
                                                            )
                                                        )
                                                    )
                                                )
                                            ),
                                            'settings_paypal_payflow' => array(
                                                'groups' => array(
                                                    'fields' => array(
                                                        'payment_action' => array(
                                                            'value' => 'Authorization'
                                                        )
                                                    ),
                                                    'settings_paypal_payflow_advanced' => array(
                                                        'fields' => array(
                                                            'useccv' => array(
                                                                'value' => 1
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
                )
            )
        );
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

    /**
     * Enable Mysql search
     *
     * @return array
     */
    protected function _getMysqlSearchEnabled()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'catalog' => array(
                        'section' => 'catalog',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'search' => array(
                                'fields' => array(
                                    'engine' => array(
                                        'value' => 'Magento\CatalogSearch\Model\Resource\Fulltext\Engine' //MySql Fulltext
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
     * Enable Check/Money order
     *
     * @return array
     */
    protected function getCheckmo()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'payment' => array(
                        'section' => 'payment',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'checkmo' => array( //Credit Card (Authorize.net)
                                'fields' => array(
                                    'active' => array(
                                        'value' => 1, //Yes
                                    ),
                                    'order_status' => array(
                                        'value' => 'pending', //New Order Status
                                    ),
                                    'allowspecific' => array(
                                        'value' => 0, //All Allowed Counries
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * Enable 3D Secure Credit Card Validation
     *
     * @return array
     */
    protected function _get3dSecureCreditCardValidation()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'payment_services' => array(
                        'section' => 'payment_services',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'centinel' => array( //3D Secure Credit Card Validation
                                'fields' => array(
                                    'processor_id' => array(
                                        'value' => '134-01'
                                    ),
                                    'merchant_id' => array(
                                        'value' => 'magentoTEST'
                                    ),
                                    'password' => array(
                                        'value' => 'mag3nt0T3ST'
                                    ),
                                    'test_mode' => array(
                                        'value' => 1 //Yes
                                    ),
                                    'debug' => array(
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

    /**
     * Data for PayPal Payments Pro Edition method with 3D Secure
     */
    protected function _getPayPalPaymentsPro3dSecure()
    {
        $data = array(
            'data' => array(
                'sections' => array(
                    'payment' => array(
                        'section' => 'payment',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'paypal_group_all_in_one' => array( // PayPal All-in-One Payment Solutions
                                'groups' => array(
                                    'wpp_us' => array( // Payments Pro (Includes Express Checkout)
                                        'groups' => array(
                                            'wpp_settings' => array( // Basic Settings - PayPal Express Checkout
                                                'groups' => array(
                                                    'wpp_settings_advanced' => array( // Advanced Settings
                                                        'fields' => array(
                                                            'centinel' => array( // 3D Secure Card Validation
                                                                'value' => 1
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
        return array_merge_recursive($data, $this->_getPaypalDirect());
    }

    /**
     * General store and country options settings
     *
     * @return array
     */
    public function getGeneralStore()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'general' => array(
                        'section' => 'general',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'store_information' => array(
                                'fields' => array(
                                    'name' => array(
                                        'value' => 'Test',
                                    ),
                                    'phone' => array(
                                        'value' => '630-371-7008',
                                    ),
                                    'country_id' => array(
                                        'value' => 'DE',
                                    ),
                                    'region_id' => array(
                                        'value' => 82,
                                    ),
                                    'postcode' => array(
                                        'value' => '10789',
                                    ),
                                    'city' => array(
                                        'value' => 'Berlin',
                                    ),
                                    'street_line1' => array(
                                        'value' => 'Augsburger Strabe 41',
                                    ),
                                    'merchant_vat_number' => array(
                                        'value' => '111607872'
                                    ),
                                ),
                            ),
                            'country' => array(
                                'fields' => array(
                                    'eu_countries' => array(
                                        'value' => array('FR', 'DE', 'GB'),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * General store and country options settings
     *
     * @return array
     */
    public function getCustomerVat()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'customer' => array(
                        'section' => 'customer',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'create_account' => array(
                                'fields' => array(
                                    'auto_group_assign' => array(
                                        'value' => 1,
                                    ),
                                    'tax_calculation_address_type' => array(
                                        'value' => 'billing',
                                    ),
                                    'viv_domestic_group' => array(
                                        'value' => 82,
                                    ),
                                    'viv_intra_union_group' => array(
                                        'value' => '10789',
                                    ),
                                    'viv_invalid_group' => array(
                                        'value' => 'Berlin',
                                    ),
                                    'viv_error_group' => array(
                                        'value' => 'Augsburger Strabe 41',
                                    ),
                                    'vat_frontend_visibility' => array(
                                        'value' => 1,
                                    ),
                                ),
                            ),
                            'country' => array(
                                'fields' => array(
                                    'eu_countries' => array(
                                        'value' => array('FR', 'DE', 'GB'),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
}
