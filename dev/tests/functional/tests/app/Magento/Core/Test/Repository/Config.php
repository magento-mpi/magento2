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
        $this->_data['authorizenet_disable'] = $this->_getAuthorizeNetDisable();
        $this->_data['authorizenet'] = $this->_getAuthorizeNet();
        $this->_data['paypal_payflow'] = $this->_getPayPalPayflow();
        //Shipping settings
        $this->_data['shipping_origin_us'] = $this->_getShippingOriginUs();
        //Shipping methods
        $this->_data['flat_rate'] = $this->_getFlatRate();
        $this->_data['free_shipping'] = $this->_getFreeShipping();
        $this->_data['shipping_disable_all_carriers'] = $this->_disableAllShippingCarriers();
        $this->_data['shipping_carrier_ups'] = $this->_getShippingCarrierUps();
        //Catalog
        $this->_data['enable_mysql_search'] = $this->_getMysqlSearchEnabled();
    }

    /**
     * Set Shipping Settings Origin to US.
     *
     * @return array
     */
    protected function _getShippingOriginUs()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'shipping' => array(
                        'section' => 'shipping',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'origin' => array(
                                'fields' => array(
                                    'country_id' => array( //Country
                                        'value' => 'US'
                                    ),
                                    'region_id' => array( //Region/State
                                        'value' => '12' //California
                                    ),
                                    'postcode' => array( //Zip/Postal Code
                                        'value' => '90232'
                                    ),
                                    'city' => array( //City
                                        'value' => 'Culver City'
                                    ),
                                    'street_line1' => array( //Street Address
                                        'value' => '10441 Jefferson Blvd'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
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

    protected function _getShippingCarrierUps()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'carriers' => array(
                        'section' => 'carriers',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'ups' => array(
                                'fields' => array(
                                    'active' => array( //Enabled for Checkout
                                        'value' => 1 //Yes
                                    ),
                                    'active_rma' => array( //Enabled for RMA
                                        'value' => 0 //No
                                    ),
                                    'type' => array( //UPS Type
                                        'value' => 'UPS_XML' //United Parcel Service XML
                                    ),
                                    'is_account_live' => array( //Live account
                                        'value' => 0 //No
                                    ),
                                    'password' => array( //Password
                                        'value' => 'magento200'
                                    ),
                                    'username' => array( //User ID
                                        'value' => 'magento'
                                    ),
                                    'mode_xml' => array( //Mode
                                        'value' => 0 //Development
                                    ),
                                    'gateway_xml_url' => array( //Gateway XML URL
                                        'value' => 'https://wwwcie.ups.com/ups.app/xml/Rate'
                                    ),
                                    'origin_shipment' => array( //Origin of the Shipment
                                        'value' => 'Shipments Originating in United States'
                                    ),
                                    'access_license_number' => array( //Access License Number
                                        'value' => 'ECAB751ABF189ECA'
                                    ),
                                    'negotiated_active' => array( //Enable Negotiated Rates
                                        'value' => 0 //No
                                    ),
                                    'shipper_number' => array( //Shipper Number
                                        'value' => '207W88'
                                    ),
                                    'container' => array( //Container
                                        'value' => 'CP' //Customer Packaging
                                    ),
                                    'dest_type' => array( //Destination Type
                                        'value' => 'RES' //Residential
                                    ),
                                    'tracking_xml_url' => array( //Tracking XML URL
                                        'value' => 'https://wwwcie.ups.com/ups.app/xml/Track'
                                    ),
                                    'unit_of_measure' => array( //Weight Unit
                                        'value' => 'LBS'
                                    ),
                                    'allowed_methods' => array( //Allowed Methods
                                        'value' => ['11','12','14','54','59','65','01','02','03','07','08']//Select all
                                    ),
                                    'sallowspecific' => array( //Ship to Applicable Countries
                                        'value' => 0 //All Allowed Countries
                                    ),
                                    'showmethod' => array( //Show Method if Not Applicable
                                        'value' => 0 //No
                                    ),
                                    'debug' => array( //Debug
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

    /**
     * Disable all shipping carriers
     *
     * @return array
     */
    protected function _disableAllShippingCarriers()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'carriers' => array(
                        'section' => 'carriers',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'ups' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => 0
                                    )
                                )
                            ),
                            'usps' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => 0
                                    )
                                )
                            ),
                            'fedex' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => 0
                                    )
                                )
                            ),
                            'dhl' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => 0
                                    )
                                )
                            ),
                            'dhlint' => array(
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
                                        'value' => 1 //Excluding Tax
                                    ),
                                    'subtotal' => array( // Display Subtotal
                                        'value' => 1 //Excluding Tax
                                    ),
                                    'shipping' => array( // Display Shipping Amount
                                        'value' => 1 //Excluding Tax
                                    ),
                                    'gift_wrapping' => array( // Display Gift Wrapping Prices
                                        'value' => 1 //Excluding Tax
                                    ),
                                    'printed_card' => array( // Display Printed Card Prices
                                        'value' => 1 //Excluding Tax
                                    ),
                                    'grandtotal' => array( // Include Tax In Grand Total
                                        'value' => 0 //No
                                    ),
                                    'full_summary' => array( // Display Full Tax Summary
                                        'value' => 0 //No
                                    ),
                                    'zero_tax' => array( // Display Zero Tax Subtotal
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
}
