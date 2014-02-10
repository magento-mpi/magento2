<?php
/**
 * {license_notice}
 *
 * @spi
 * @category Mtf
 * @package Mtf
 * @subpackage functional_tests
 * @copyright {copyright}
 * @license {license_link}
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
    /**
     * Configuration option constants
     */
    const EXCLUDING_TAX = 1;
    const NO_VALUE = 0;
    const YES_VALUE = 1;

    /**
     * Config repository constructor
     *
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );
        // General
        $this->_data['general_store_information'] = $this->getGeneralStoreGermany();
        // Currency Setup
        $this->_data['currency_usd'] = $this->_getCurrencyUSD();
        //Tax
        $this->_data['default_tax_config'] = $this->_getDefaultTax();
        $this->_data['us_tax_config'] = $this->_getUsTax();
        $this->_data['display_price'] = $this->_getPriceDisplay();
        $this->_data['display_shopping_cart'] = $this->_getShoppingCartDisplay();
        //Payment methods
        $this->_data['paypal_express'] = $this->_getPaypalExpress();
        $this->_data['paypal_direct'] = $this->_getPaypalDirect();
        $this->_data['paypal_disabled_all_methods'] = $this->_getPaypalDisabled();
        $this->_data['paypal_payflow_link'] = $this->_getPaypalPayFlowLink();
        $this->_data['paypal_payflow_pro'] = $this->_getPaypalPayFlowPro();
        $this->_data['paypal_advanced'] = $this->_getPaypalAdvanced();
        $this->_data['paypal_standard'] = $this->_getPaypalStandard();
        $this->_data['paypal_payflow_pro_3d_secure'] = $this->_getPayPalPayflowPro3dSecure();
        $this->_data['paypal_payments_pro_3d_secure'] = $this->_getPayPalPaymentsPro3dSecure();
        $this->_data['authorizenet_disable'] = $this->_getAuthorizeNetDisable();
        $this->_data['authorizenet'] = $this->_getAuthorizeNet();
        $this->_data['authorizenet_3d_secure'] = $this->_getAuthorizeNet3dSecure();
        $this->_data['paypal_payflow'] = $this->_getPayPalPayflow();
        //Payment Services
        $this->_data['3d_secure_credit_card_validation'] = $this->_get3dSecureCreditCardValidation();
        //Shipping settings
        $this->_data['shipping_origin_us'] = $this->_getShippingOriginUS();
        //Shipping methods
        $this->_data['flat_rate'] = $this->_getFlatRate();
        $this->_data['free_shipping'] = $this->_getFreeShipping();
        $this->_data['shipping_disable_all_carriers'] = $this->_disableAllShippingCarriers();
        $this->_data['shipping_carrier_dhlint_eu'] = $this->_getShippingCarrierDhlIntEU();
        $this->_data['shipping_carrier_dhlint_uk'] = $this->_getShippingCarrierDhlIntUK();
        $this->_data['shipping_carrier_fedex'] = $this->_getShippingCarrierFedex();
        $this->_data['shipping_carrier_ups'] = $this->_getShippingCarrierUps();
        $this->_data['shipping_carrier_usps'] = $this->_getShippingCarrierUsps();
        //Catalog
        $this->_data['enable_mysql_search'] = $this->_getMysqlSearchEnabled();
        $this->_data['check_money_order'] = $this->getCheckmo();
        $this->_data['show_out_of_stock'] = $this->_getShowOutOfStock();
        $this->_data['enable_product_flat'] = $this->_getProductFlatEnabled();
        //Sales
        $this->_data['enable_map_config'] = $this->_getMapEnabled();
        $this->_data['disable_secret_key'] = $this->_getSecretKeyEnabled();
        $this->_data['disable_map_config'] = $this->_getMapDisabled();
        $this->_data['enable_rma'] = $this->_getRmaEnabled();
        //Customer
        $this->_data['address_template'] = $this->_getAddressTemplate();
        $this->_data['customer_disable_group_assign'] = $this->getDisableGroupAssignData();
        //Currency Setup
        $this->_data['allowed_currencies'] = $this->_getAllowedCurrencies();
    }

    /**
     * Set Currency to value passed in.
     *
     * @return array
     */
    protected function _getCurrencyUSD()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'currency' => array(
                        'section' => 'currency',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'options' => array(
                                'fields' => array(
                                    'allow' => array( //Allowed Currencies
                                        'value' => ['USD']
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
     * Set Shipping Settings Origin to US.
     *
     * @return array
     */
    protected function _getShippingOriginUS()
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
                                    ),
                                    'street_line2' => array( //Street Address Line 2
                                        'value' => 'Suite 200'
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
                                        'value' => self::YES_VALUE
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
                                        'value' => self::YES_VALUE
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
                                        'value' => self::YES_VALUE
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

    /**
     * DHL International shipping method configuration.
     * Specifically for testing shipping origin of EA (Europe) Region.
     * Shipping origin is specifically set to Switzerland with currency of Swiss Franc.
     *
     * @return array
     */
    protected function _getShippingCarrierDhlIntEU()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'carriers' => array(
                        'section' => 'carriers',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'dhlint' => array(
                                'fields' => array(
                                    'active' => array( //Enabled for Checkout
                                        'value' => self::YES_VALUE
                                    ),
                                    'gateway_url' => array( //Gateway URL
                                        'value' => 'https://xmlpitest-ea.dhl.com/XMLShippingServlet'
                                    ),
                                    'id' => array( //Access ID
                                        'value' => 'EvgeniyDE'
                                    ),
                                    'password' => array( //Password
                                        'value' => 'aplNb6Rop'
                                    ),
                                    'account' => array( //Account Number
                                        'value' => '152691811'
                                    ),
                                    'showmethod' => array( //Show Method if Not Applicable
                                        'value' => self::YES_VALUE
                                    ),
                                    'debug' => array( //Debug
                                        'value' => self::YES_VALUE
                                    )
                                )
                            )
                        )
                    ),
                    'shipping' => array(
                        'section' => 'shipping',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'origin' => array(
                                'fields' => array(
                                    'country_id' => array( //Country
                                        'value' => 'CH' //Switzerland
                                    ),
                                    'region_id' => array( //Region/State
                                        'value' => '107' //Bern
                                    ),
                                    'postcode' => array( //Zip/Postal Code
                                        'value' => '3005'
                                    ),
                                    'city' => array( //City
                                        'value' => 'Bern'
                                    ),
                                    'street_line1' => array( //Street Address
                                        'value' => 'Weinbergstrasse 4'
                                    ),
                                    'street_line2' => array( //Street Address Line 2
                                        'value' => 'Suite 1'
                                    )
                                )
                            )
                        )
                    ),
                    'currency' => array(
                        'section' => 'currency',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'options' => array(
                                'fields' => array(
                                    'allow' => array( //Allowed Currencies
                                        'value' => ['USD','CHF']
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
     * DHL International shipping method configuration.
     * Specifically for testing domestic shipping within the UK.
     *
     * @return array
     */
    protected function _getShippingCarrierDhlIntUK()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'carriers' => array(
                        'section' => 'carriers',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'dhlint' => array(
                                'fields' => array(
                                    'active' => array( //Enabled for Checkout
                                        'value' => self::YES_VALUE
                                    ),
                                    'gateway_url' => array( //Gateway URL
                                        'value' => 'https://xmlpitest-ea.dhl.com/XMLShippingServlet'
                                    ),
                                    'id' => array( //Access ID
                                        'value' => 'EvgeniyDE'
                                    ),
                                    'password' => array( //Password
                                        'value' => 'aplNb6Rop'
                                    ),
                                    'account' => array( //Account Number
                                        'value' => '152691811'
                                    ),
                                    'showmethod' => array( //Show Method if Not Applicable
                                        'value' => self::YES_VALUE
                                    ),
                                    'debug' => array( //Debug
                                        'value' => self::YES_VALUE
                                    )
                                )
                            )
                        )
                    ),
                    'shipping' => array(
                        'section' => 'shipping',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'origin' => array(
                                'fields' => array(
                                    'country_id' => array( //Country
                                        'value' => 'GB' //United Kingdom
                                    ),
                                    'region_id' => array( //Region/State
                                        'value' => 'London'
                                    ),
                                    'postcode' => array( //Zip/Postal Code
                                        'value' => 'SE10 8SE'
                                    ),
                                    'city' => array( //City
                                        'value' => 'London'
                                    ),
                                    'street_line1' => array( //Street Address
                                        'value' => '89 Royal Hill'
                                    )
                                )
                            )
                        )
                    ),
                    'currency' => array(
                        'section' => 'currency',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'options' => array(
                                'fields' => array(
                                    'allow' => array( //Allowed Currencies
                                        'value' => ['USD','GBP']
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
     * Fedex shipping method configuration as well as a real US address set in specified
     * in shipping settings origin.
     *
     * @return array
     */
    protected function _getShippingCarrierFedex()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'carriers' => array(
                        'section' => 'carriers',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'fedex' => array(
                                'fields' => array(
                                    'active' => array( //Enabled for Checkout
                                        'value' => self::YES_VALUE
                                    ),
                                    'account' => array( //Account ID
                                        'value' => '510087801'
                                    ),
                                    'meter_number' => array( //Meter Number
                                        'value' => '100047915'
                                    ),
                                    'key' => array( //Key
                                        'value' => 'INdxa6ug7qZ2KD7y'
                                    ),
                                    'password' => array( //Password
                                        'value' => '4vaGsDBmeBCzvpl1S1DT1jXAB'
                                    ),
                                    'sandbox_mode' => array( //Sandbox Mode
                                        'value' => self::YES_VALUE
                                    )
                                )
                            )
                        )
                    ),
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
                                        'value' => '90024'
                                    ),
                                    'city' => array( //City
                                        'value' => 'Los Angeles'
                                    ),
                                    'street_line1' => array( //Street Address
                                        'value' => '1419 Westwood Blvd'
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
                                        'value' => self::YES_VALUE
                                    ),
                                    'active_rma' => array( //Enabled for RMA
                                        'value' => self::NO_VALUE
                                    ),
                                    'type' => array( //UPS Type
                                        'value' => 'UPS_XML' //United Parcel Service XML
                                    ),
                                    'is_account_live' => array( //Live account
                                        'value' => self::NO_VALUE
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
                                        'value' => self::NO_VALUE
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
                                        'value' => self::NO_VALUE
                                    ),
                                    'debug' => array( //Debug
                                        'value' => self::YES_VALUE
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    protected function _getShippingCarrierUsps()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'carriers' => array(
                        'section' => 'carriers',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'usps' => array(
                                'fields' => array(
                                    'active' => array( //Enabled for Checkout
                                        'value' => self::YES_VALUE
                                    ),
                                    'gateway_url' => array( //Gateway URL
                                        'value' => 'http://production.shippingapis.com/ShippingAPI.dll'
                                    ),
                                    'gateway_secure_url' => array( //Secure Gateway URL
                                        'value' => 'https://secure.shippingapis.com/ShippingAPI.dll'
                                    ),
                                    'userid' => array( //User ID
                                        'value' => '721FRAGR6267'
                                    ),
                                    'password' => array( //Password
                                        'value' => '326ZL84XF990'
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
                                    'active' => array( //Enabled for Checkout
                                        'value' => self::NO_VALUE
                                    )
                                )
                            ),
                            'usps' => array(
                                'fields' => array(
                                    'active' => array( //Enabled for Checkout
                                        'value' => self::NO_VALUE
                                    )
                                )
                            ),
                            'fedex' => array(
                                'fields' => array(
                                    'active' => array( //Enabled for Checkout
                                        'value' => self::NO_VALUE
                                    )
                                )
                            ),
                            'dhl' => array(
                                'fields' => array(
                                    'active' => array( //Enabled for Checkout
                                        'value' => self::NO_VALUE
                                    )
                                )
                            ),
                            'dhlint' => array(
                                'fields' => array(
                                    'active' => array( //Enabled for Checkout
                                        'value' => self::NO_VALUE
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
                                        'value' => self::YES_VALUE
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
                                        'value' => self::NO_VALUE
                                    ),
                                    'cctypes' => array( //Card Types
                                        'value' => 'AE,VI,MC,DI' //American Express, Visa, MasterCard, Discover
                                    ),
                                    'useccv' => array( //Credit Card Verification
                                        'value' => self::YES_VALUE
                                    ),
                                    'centinel' => array( //3D Secure Card Validation
                                        'value' => self::NO_VALUE
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
                                        'value' => self::NO_VALUE
                                    )
                                )
                            ),
                            'paypal_standard' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => self::NO_VALUE
                                    )
                                )
                            ),
                            'paypal_direct' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => self::NO_VALUE
                                    )
                                )
                            ),
                            'payflowpro' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => self::NO_VALUE
                                    )
                                )
                            ),
                            'payflow_express' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => self::NO_VALUE
                                    )
                                )
                            ),
                            'payflow_advanced' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => self::NO_VALUE
                                    )
                                )
                            ),
                            'payflow_link' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => self::NO_VALUE
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
                                                                'value' => self::YES_VALUE
                                                            ),
                                                            'use_proxy' => array( //API Uses Proxy
                                                                'value' => self::NO_VALUE
                                                            )
                                                        )
                                                    )
                                                ),
                                                'fields' => array(
                                                    'enable_wpp' => array( //Enable this Solution
                                                        'value' => self::YES_VALUE
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
                                        'value' => self::YES_VALUE
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }
    protected function _getPaypalAdvanced()
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
                                    'payflow_advanced_us' => array( //PayPal Payments Advanced (Includes Express Checkout)
                                        'groups' => array(
                                            'required_settings' => array( //Required PayPal Settings
                                                'groups' => array(
                                                    'payments_advanced' => array( //Payments Pro and Express Checkout
                                                        'fields' => array(
                                                            'business_account' => array( //Email Associated with PayPal
                                                                'value' => 'rlus_1349181941_biz@ebay.com'
                                                            ),
                                                            'partner' => array( //Partner
                                                                'value' => 'PayPal'
                                                            ),
                                                            'vendor' => array( //Vendor
                                                                'value' => 'mpiteamadvanced123'
                                                            ),
                                                            'user' => array( //User
                                                                'value' => 'mpiteamadvanced123'
                                                            ),
                                                            'pwd' => array( //Password
                                                                'value' => 'Temp1234'
                                                            ),
                                                            'sandbox_flag' => array( //Test Mode
                                                                'value' => self::YES_VALUE
                                                            ),
                                                            'use_proxy' => array( //Use Proxy
                                                                'value' => self::NO_VALUE
                                                            )
                                                        )
                                                    )
                                                ),
                                                'fields' => array(
                                                    'enable_payflow_advanced' => array( //Enable this Solution
                                                        'value' => self::YES_VALUE
                                                    )
                                                )
                                            ),
                                            'settings_payments_advanced' => array( //Basic Settings - PayPal Payments Pro
                                                'fields' => array(
                                                    'payment_action' => array( //Payment Action
                                                        'value' => 'Authorization' //Authorization
                                                    )
                                                ),
                                            )
                                        )
                                    )
                                )
                            ),
                            'paypal_express' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => self::YES_VALUE
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    protected function _getPaypalStandard()
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
                                    'wps_us' => array( //Payments Standard
                                        'groups' => array(
                                            'wps_required_settings' => array( //Required PayPal Settings
                                                'fields' => array(
                                                    'business_account' => array( //Email Associated with PayPal Merchant Account
                                                        'value' => 'rlus_1349181941_biz@ebay.com'
                                                    ),
                                                    'enable_wps' => array( //Enable this Solution
                                                        'value' => 1 //Yes
                                                    )
                                                )
                                            ),
                                            'settings_payments_standart' => array( //Basic Settings - PayPal Payments Standard
                                                'fields' => array(
                                                    'payment_action' => array( //Payment Action
                                                        'value' => 'Sale' //Sale
                                                    )
                                                ),
                                                'groups' => array(
                                                    'settings_payments_standart_advanced' => array(
                                                        'fields' => array(
                                                            'allowspecific' => array(  // Payment Applicable From
                                                                'value' => 'All Allowed Countries'
                                                            ),
                                                            'sandbox_flag' => array( //Sandbox Mode
                                                                'value' => 1 //Yes
                                                            ),
                                                            'line_items_enabled' => array( //Transfer Cart Line Items
                                                                'value' => 1 //Yes
                                                            ),
                                                            'verify_peer' => array( //Enable SSL verification
                                                                'value' => 0 //No
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
                                                                'value' => self::YES_VALUE
                                                            ),
                                                            'use_proxy' => array( //API Uses Proxy
                                                                'value' => self::NO_VALUE
                                                            )
                                                        )
                                                    )
                                                ),
                                                'fields' => array(
                                                    'enable_express_checkout' => array( //Enable this Solution
                                                        'value' => self::YES_VALUE
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
     * Get Configuration Settings for PayPal Payflow Link Payment Method
     *
     * @return array
     */
    protected function _getPaypalPayFlowLink()
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
                                    'payflow_link_us' => array( // Payflow Link(Includes Express Checkout)
                                        'groups' => array(
                                            'payflow_link_required' => array( // Required Paypal Settings
                                                'groups' => array(
                                                    'payflow_link_payflow_link' => array( // Payflow Link and Express Checkout
                                                        'fields' => array(
                                                            'business_account' => array( // Email Associated with PayPal Merchant Account
                                                                'value' => 'rlus_1349181941_biz@ebay.com'
                                                            ),
                                                            'partner' => array( // Partner
                                                                'value' => 'PayPal'
                                                            ),
                                                            'user' => array( // API User
                                                                'value' => 'rlpayflowlinknew'
                                                            ),
                                                            'vendor' => array( // Vendor
                                                                'value' => 'rlpayflowlinknew'
                                                            ),
                                                            'pwd' => array( // API Password
                                                                'value' => 'Temp1234'
                                                            ),
                                                            'sandbox_flag' => array( // Test Mode
                                                                'value' => self::YES_VALUE
                                                            ),
                                                            'use_proxy' => array( // Use Proxy
                                                                'value' => self::NO_VALUE
                                                            )
                                                        )
                                                    )
                                                ),
                                                'fields' => array(
                                                    'enable_payflow_link' => array( // Enable this solution
                                                        'value' => self::YES_VALUE
                                                    ),
                                                    'enable_express_checkout' => array( // Enable this solution
                                                        'value' => self::YES_VALUE
                                                    )
                                                )
                                            ),
                                            'settings_payflow_link' => array( // Basic Settings - PayPal Payflow Link
                                                'fields' => array(
                                                    'payment_action' => array( // Payment Action
                                                        'value' => 'Authorization'
                                                    )
                                                )
                                            ),
                                            'settings_payflow_link_express_checkout' => array( // Basic Settings - PayPal Express Checkout
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
                                    'paypal_payflowpro_with_express_checkout_us' => array( // Payflow Pro (Includes Express Checkout)
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
                                                                'value' => self::YES_VALUE
                                                            ),
                                                            'use_proxy' => array( // Use Proxy
                                                                'value' => self::NO_VALUE
                                                            )
                                                        )
                                                    )
                                                ),
                                                'fields' => array(
                                                    'enable_paypal_payflow' => array( //Enable this Solution
                                                        'value' => self::YES_VALUE
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
                            'payflow_express' => array(
                                'fields' => array(
                                    'active' => array(
                                        'value' => self::YES_VALUE
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
        $data = array(
            'data' => array(
                'sections' => array(
                    'payment' => array(
                        'section' => 'payment',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'paypal_payment_gateways' => array(
                                'groups' => array(
                                    'paypal_payflowpro_with_express_checkout_us' => array(
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
                                        'value' => self::EXCLUDING_TAX
                                    ),
                                    'shipping' => array( // Display Shipping Prices
                                        'value' => self::EXCLUDING_TAX
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
                                        'value' => self::EXCLUDING_TAX
                                    ),
                                    'subtotal' => array( // Display Subtotal
                                        'value' => self::EXCLUDING_TAX
                                    ),
                                    'shipping' => array( // Display Shipping Amount
                                        'value' => self::EXCLUDING_TAX
                                    ),
                                    'gift_wrapping' => array( // Display Gift Wrapping Prices
                                        'value' => self::EXCLUDING_TAX
                                    ),
                                    'printed_card' => array( // Display Printed Card Prices
                                        'value' => self::EXCLUDING_TAX
                                    ),
                                    'grandtotal' => array( // Include Tax In Grand Total
                                        'value' => self::NO_VALUE
                                    ),
                                    'full_summary' => array( // Display Full Tax Summary
                                        'value' => self::NO_VALUE
                                    ),
                                    'zero_tax' => array( // Display Zero Tax Subtotal
                                        'value' => self::NO_VALUE
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
                                    'paypal_payflowpro_with_express_checkout_us' => array(
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
                                        'value' => self::NO_VALUE
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
                                        'value' => self::YES_VALUE,
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
     * Enable MAP
     *
     * @return array
     */
    protected function _getMapEnabled()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'sales' => array(
                        'section' => 'sales',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'msrp' => array( //Minimum Advertised Price)
                                'fields' => array(
                                    'enabled' => array( //Enabled
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
     * Disable MAP
     *
     * @return array
     */
    protected function _getMapDisabled()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'sales' => array(
                        'section' => 'sales',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'msrp' => array( //Minimum Advertised Price)
                                'fields' => array(
                                    'enabled' => array( //Disabled
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
     * Data for address template configuration
     *
     * @return array
     */
    protected function _getAddressTemplate()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'customer' => array(
                        'section' => 'customer',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'address_templates' => array(
                                'fields' => array(
                                    'oneline' => array(
                                        'value' => '{{var firstname}} {{var lastname}}, {{var street}}, {{var city}},'
                                            . ' {{var region}} {{var postcode}}, {{var country}}'
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
     * Disable Secret Key
     *
     * @return array
     */
    protected function _getSecretKeyEnabled()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'admin' => array(
                        'section' => 'admin',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'security' => array(
                                'fields' => array(
                                    'use_form_key' => array(
                                        'value' => '0'
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
     * General store and country options settings
     *
     * @return array
     */
    public function getGeneralStoreGermany()
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
     * Get data for disable automatic assignment customer to customer group
     *
     * @return array
     */
    public function getDisableGroupAssignData()
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
                                        'value' => self::NO_VALUE,
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
     * Set allowed currency
     *
     * @return array
     */
    protected function _getAllowedCurrencies()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'currency' => array(
                        'section' => 'currency',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'options' => array(
                                'fields' => array(
                                    'allow' => array(
                                        'value' => array('EUR', 'USD')
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
     * Enable RMA
     *
     * @return array
     */
    protected function _getRmaEnabled()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'sales' => array(
                        'section' => 'sales',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'magento_rma' => array( //RMA
                                'fields' => array(
                                    'enabled' => array( //Enabled
                                        'value' => 1 //Yes
                                    ),
                                    'enabled_on_product' => array( //Enabled
                                        'value' => 1 //Yes
                                    ),
                                    'use_store_address' => array( //Disabled
                                        'value' => 0 //No
                                    ),
                                    'store_name' => array(
                                        'value' => 'Return Store'
                                    ),
                                    'address' => array(
                                        'value' => 'Main Street 1'
                                    ),
                                    'city' => array( //New York
                                        'value' => 'New York'
                                    ),
                                    'region_id' => array( // New York
                                        'value' => '43'
                                    ),
                                    'zip' => array(
                                        'value' =>'10010'
                                    ),
                                    'country_id' => array( // United States
                                        'value' => 'US'
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
     * Enable product flat
     *
     * @return array
     */
    public function _getProductFlatEnabled()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'catalog' => array(
                        'section' => 'catalog',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'frontend' => array( //Frontend
                                'fields' => array(
                                    'flat_catalog_product' => array( //Enabled
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
     * Enable 'Display out of Stock' option for catalog
     *
     * @return array
     */
    protected function _getShowOutOfStock()
    {
        return array(
            'data' => array(
                'sections' => array(
                    'cataloginventory' => array(
                        'section' => 'cataloginventory',
                        'website' => null,
                        'store' => null,
                        'groups' => array(
                            'options' => array( //Stock Options
                                'fields' => array(
                                    'show_out_of_stock' => array(
                                        'value' => 1, //Yes
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
