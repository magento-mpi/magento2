<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Pbridge_Model_Pbridge_Merchant
{
    /**
     * Methods that can be updated
     *
     * @var string
     */
    const METHOD_ALL_PAYMENTS     = 'payments';
    const METHOD_ALL_SERVICES     = 'services';
    const METHOD_WPP_DIRECT       = 'paypal_direct';
    const METHOD_WPP_PE_DIRECT    = 'paypaluk_direct';
    const METHOD_PAYFLOWPRO       = 'verisign';
    const METHOD_AUTHORIZENET     = 'authorizenet';
    const METHOD_CENTINEL         = 'centinel';
    const METHOD_KOUNT            = 'kount';
    const METHOD_OGONE_DIRECT     = 'pbridge_ogone_direct';
    const METHOD_OGONE_DIRECT_DEBIT = 'pbridge_ogone_direct_debit';
    const METHOD_SAGEPAY_DIRECT   = 'sagepay_direct';
    const METHOD_PSIGATE_BASIC    = 'pbridge_psigate_basic';
    const METHOD_PAYONE_GATE      = 'payone_gate';
    const METHOD_PAYONE_DEBIT     = 'pbridge_payone_debit';
    const METHOD_PAYBOX_DIRECT    = 'paybox_direct';
    const METHOD_DIBS             = 'dibs';
    const METHOD_CYBERSOURCE      = 'sybersource_soap';
    const METHOD_EWAY             = 'eway_direct';
    const METHOD_BRAINTREE_BASIC  = 'pbridge_braintree_basic';
    const METHOD_FIRSTDATA        = 'firstdata';
    const METHOD_WORLDPAY_DIRECT  = 'worldpay_direct';
    const METHOD_WPP_DIRECT_BOARDING = 'paypal_direct_boarding';
    /**
     * Payments configuration values map
     *
     * array(
     *     'payemnt_code' => array(
     *         'field_name_in_pbridge' => 'field_name_in_magento'
     *     )
     * )
     *
     * @var array
     */
    protected $_configurationMap = array(
        'authorizenet' => array(
            'active' => 'active',
            'title' => 'title',
            'cctypes' => 'cctypes',
            'useccv'  => 'useccv',
            'centinel' => 'centinel',
            'centinel_backend' => 'centinel_backend',
            'centinel_is_mode_strict' => 'centinel_is_mode_strict',
            'centinel_api_url' => 'centinel_api_url',
            'email_customer' => 'email_customer',
            'login' => 'login',
            'merchant_email' => 'merchant_email',
            'test' => 'test',
            'trans_key' => 'trans_key',
            'payment_profiles_enabled'=>'payment_profiles_enabled',
            'transaction_id_prefix' => 'transaction_id_prefix'
        ),
        'paypal_direct' => array(
            'title' => 'title',
            'api_authentication' => 'api_authentication',
            'api_username' => 'api_username',
            'api_password' => 'api_password',
            'api_signature' => 'api_signature',
            'line_items_enabled' => 'line_items_enabled',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'centinel' => 'centinel',
            'centinel_backend' => 'centinel_backend',
            'centinel_is_mode_strict' => 'centinel_is_mode_strict',
            'centinel_api_url' => 'centinel_api_url',
            'sandbox_flag' => 'sandbox_flag',
            'use_proxy' => 'use_proxy',
            'proxy_url' => 'proxy_host',
            'proxy_port' => 'proxy_port',
        ),
        'paypaluk_direct' => array(
            'title' => 'title',
            'partner' => 'partner',
            'user' => 'user',
            'vendor' => 'vendor',
            'password' => 'pwd',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'line_items_enabled' => 'line_items_enabled',
            'centinel' => 'centinel',
            'centinel_backend' => 'centinel_backend',
            'centinel_is_mode_strict' => 'centinel_is_mode_strict',
            'centinel_api_url' => 'centinel_api_url',
            'sandbox_flag' => 'sandbox_flag',
        ),
        'verisign' => array(
            'title' => 'title',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'centinel' => 'centinel',
            'centinel_backend' => 'centinel_backend',
            'is_mode_strict' => 'centinel_is_mode_strict',
            'centinel_api_url' => 'centinel_api_url',
            'partner' => 'partner',
            'user' => 'user',
            'vendor' => 'vendor',
            'pwd' => 'pwd',
            'use_proxy' => 'use_proxy',
            'proxy_host' => 'proxy_host',
            'proxy_port' => 'proxy_port',
            'tender' => 'tender',
            'verbosity' => 'verbosity',
            'url' => array('handler' => '_prepareVerisignGatewayUrl')
        ),
        'centinel' => array(
            'title' => 'title',
            'processor_id' => 'processor_id',
            'merchant_id' => 'merchant_id',
            'password' => array('handler' => '_prepareCentinelPassword'),
            'test_mode' => 'test_mode'
        ),
        'kount' => array(
            'enabled' => 'enabled',
            'merchant_id' => 'merchant_id',
            'p12_cert' => 'p12_cert',
            'p12_cert_password' => 'p12_cert_password',
            'test_mode' => 'test_mode',
            'whitelist' => 'whitelist'
        ),
        'pbridge_ogone_direct' => array(
            'title' => 'title',
            'sandbox_flag' => 'sandbox_flag',
            'pspid' => 'pspid',
            'userid' => 'userid',
            'passphrase' => 'passphrase',
            'password' => 'password',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'enable3ds' => 'enable3ds'
        ),
        'pbridge_ogone_direct_debit' => array(
            'title' => 'title',
            'sandbox_flag' => 'sandbox_flag',
        ),
        'sagepay_direct' => array(
            'title' => 'title',
            'vendor_name' => 'vendor_name',
            'mode' => 'mode',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'enable3ds' => 'enable3ds',
            'transaction_id_prefix'=>'transaction_id_prefix'
        ),
        'pbridge_payone_debit' => array(
            'title' => 'title',
            'merchantid' => 'merchantid',
            'portalid' => 'portalid',
            'sub_account' => 'sub_account',
            'trans_key' => 'trans_key',
            'transaction_id_prefix' => 'transaction_id_prefix',
            'test' => 'test',
            'enable_bankcheck' => 'enable_bankcheck',
            'bankcheck_type' => 'bankcheck_type'
        ),
        'payone_gate' => array(
            'title' => 'title',
            'merchantid' => 'merchantid',
            'portalid' => 'portalid',
            'sub_account' => 'sub_account',
            'trans_key' => 'trans_key',
            'transaction_id_prefix' => 'transaction_id_prefix',
            'transaction_status_key' => 'transaction_status_key',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'enable3ds' => 'enable3ds',
            'test' => 'test'
        ),
        'dibs' => array(
            'title' => 'title',
            'merchantid' => 'merchantid',
            'login' => 'login',
            'password' => 'password',
            'key_1' => 'key_1',
            'key_2' => 'key_2',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'test' => 'test',
            'transaction_id_prefix' => 'transaction_id_prefix'
        ),
        'psigate_basic' => array(
            'title' => 'title',
            'storeid' => 'storeid',
            'passphrase' => 'passphrase',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'test' => 'test'
        ),
        'paybox_direct' => array(
            'title' => 'title',
            'pbx_site' => 'pbx_site',
            'pbx_rang' => 'pbx_rang',
            'pbx_cle' => 'pbx_cle',
            'pbx_url' => 'pbx_url',
            'pbx_backupurl' => 'pbx_backupurl',
            'cctypes' => 'cctypes',
        ),
        'cybersource_soap' => array(//$pbridgeField => $magentoField
            'title' => 'title',
            'login' => 'merchant_id',
            'email_customer' => 'email_customer',
            'merchant_email' => 'merchant_email',
            'trans_key' => 'security_key',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'enable_jpo' => 'enable_jpo',
            'jpo_method' => 'jpo_method',
            'jpo_installment_number' => 'jpo_installment_number',
            'test' => 'test'
        ),
        'eway_direct' => array(
            'title' => 'title',
            'login' => 'customer_id',
            'refunds_password' => 'refunds_password',
            'email_customer' => 'email_customer',
            'merchant_email' => 'merchant_email',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'test' => 'test'
        ),
        'braintree_basic' => array(
            'active' => 'active',
            'environment' => 'environment',
            'merchant_id' => 'merchant_id',
            'public_key' => 'public_key',
            'private_key' => 'private_key',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'payment_profiles_enabled' => 'payment_profiles_enabled',
            'title' => 'title'
        ),
        'firstdata' => array(
            'title' => 'title',
            'config_file' => 'config_file',
            'transaction_id_prefix' => 'transaction_id_prefix',
            'test' => 'test',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv'
        ),
        'pbridge_styling' => array(
            'css' => 'css'
        ),
        'worldpay_direct' => array(
            'merchant_code' => 'merchant_code',
            'xml_password' => 'xml_password',
            'account_type' => 'account_type',
            'installation_id' => 'installation_id',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'enable3ds' => 'enable3ds',
            'test' => 'test',
            'transaction_id_prefix' => 'transaction_id_prefix'
        ),
        'paypal_direct_boarding' => array(
            'api_username' => 'api_username',
            'api_password' => 'api_password',
            'api_signature' => 'api_signature',
            'line_items_enabled' => 'line_items_enabled',
            'cctypes' => 'cctypes',
            'useccv' => 'useccv',
            'centinel' => 'centinel',
            'centinel_backend' => 'centinel_backend',
            'centinel_is_mode_strict' => 'centinel_is_mode_strict',
            'centinel_api_url' => 'centinel_api_url',
            'sandbox_flag' => 'sandbox_flag',
            'use_proxy' => 'use_proxy',
            'proxy_url' => 'proxy_host',
            'proxy_port' => 'proxy_port',
            'boarding_account' => array('handler' => '_getBoardingAccount')
        )
    );


    /**
     * Update payments configuration on Pbridge side
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Pbridge_Model_Pbridge_Merchant
     */
    public function updatePbridgePaymentConfiguration(Varien_Event_Observer $observer)
    {
        $this->updatePbridgeOtherMethodsConfiguration($observer);
        $this->updatePbridgePaypalConfiguration($observer);
    }

    /**
     * Update NOT Paypal payment methods configuration on Pbridge side
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Pbridge_Model_Pbridge_Merchant
     */
    public function updatePbridgeOtherMethodsConfiguration(Varien_Event_Observer $observer)
    {
        $websiteCode = $observer->getEvent()->getWebsite();
        $storeCode   = $observer->getEvent()->getStore();

        $payments = array('authorizenet' => array('payment/authorizenet'),
            'pbridge_ogone_direct' => array('payment/pbridge_ogone_direct'),
            'pbridge_ogone_direct_debit' => array('payment/pbridge_ogone_direct_debit'),
            'payone_gate' => array('payment/payone_gate'),
            'pbridge_payone_debit' => array('payment/pbridge_payone_debit'),
            'dibs' => array('payment/dibs'),
            'sagepay_direct' => array('payment/sagepay_direct'),
            'paybox_direct' => array('payment/paybox_direct'),
            'psigate_basic' => array('payment/psigate_basic'),
            'cybersource_soap' => array('payment/cybersource_soap'),
            'eway_direct' => array('payment/eway_direct'),
            'braintree_basic' => array('payment/braintree_basic'),
            'firstdata' => array('payment/firstdata'),
            'worldpay_direct' => array('payment/worldpay_direct'),
        );

        $this->_updatePbridgeConfiguration($payments, self::METHOD_ALL_PAYMENTS, $websiteCode, $storeCode);
        $this->_updatePaymentProfileStatus();

        return $this;
    }

    /**
     * Update PayPal payments configuration on Pbridge side
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Pbridge_Model_Pbridge_Merchant
     */
    public function updatePbridgePaypalConfiguration(Varien_Event_Observer $observer)
    {
        $websiteCode = $observer->getEvent()->getWebsite();
        $storeCode   = $observer->getEvent()->getStore();

        $payments = array(
            'paypal_direct' => array('payment/paypal_direct', 'paypal/wpp', 'paypal/general', 'paypal/style'),
            'paypal_direct_boarding' => array('paypal/onboarding', 'payment/paypal_direct_boarding'),
            'paypaluk_direct' => array('payment/paypaluk_direct', 'paypal/wpuk', 'paypal/general', 'paypal/style'),
            'verisign' => array('payment/verisign')
        );

        $sectionsCfg = $this->_prepareCfgSections($payments, $websiteCode, $storeCode);

        /**
         * @var Mage_Paypal_Model_Cert $paypalCertModel
         */
        $paypalCertModel = Mage::getModel('Mage_Paypal_Model_Cert');
        $paypalCertModel->loadByWebsite(Mage::app()->getWebsite($websiteCode)->getId());
        if ($paypalCertModel->getid()) {
            $certContent = Mage::helper('Mage_Core_Helper_Data')->decrypt($paypalCertModel->getContent());
            $sectionsCfg['configuration']['paypal_direct']['api_cert'] = $certContent;
        } else {
            $sectionsCfg['configuration']['paypal_direct']['api_cert'] = '';
        }

        $this->_updatePbridgeConfiguration($sectionsCfg, self::METHOD_ALL_PAYMENTS, $websiteCode, $storeCode);

        return $this;
    }

    /**
     * Update PayPal Onboarding configuration on Pbridge side
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Pbridge_Model_Pbridge_Merchant
     */
    public function updatePbridgePaypalBoardingConfiguration(Varien_Event_Observer $observer)
    {
        $payments = array(
            'paypal_direct_boarding' => array('paypal/onboarding', 'payment/paypal_direct_boarding'),
        );

        $this->_updatePbridgeConfiguration($payments, self::METHOD_ALL_PAYMENTS, null, null);

        return $this;
    }

    /**
     * Update payment services configuration on Pbridge side
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Pbridge_Model_Pbridge_Merchant
     */
    public function updatePbridgePaymentServicesConfiguration(Varien_Event_Observer $observer)
    {
        $websiteCode = $observer->getEvent()->getWebsite();
        $storeCode   = $observer->getEvent()->getStore();

        $services = array(
            'centinel' => array('payment_services/centinel'),
            'kount' => array('payment_services/kount'),
            'pbridge_styling' => array('payment_services/pbridge_styling')
        );

        $this->_updatePbridgeConfiguration($services, self::METHOD_ALL_SERVICES, $websiteCode, $storeCode);

        return $this;
    }

    /**
     * Fetch configuration and prepare it based on payment configuration mapping
     *
     * @param array $magentoCfgSections
     * @param null $websiteCode
     * @param null $storeCode
     * @return array
     */
    protected function _prepareCfgSections(array $magentoCfgSections = array(), $websiteCode = null, $storeCode = null)
    {
        $sectionsCfg = $this->_fetchSectionsConfiguration($magentoCfgSections, $websiteCode, $storeCode);
        $variation   = $this->_prepareVariation($websiteCode, $storeCode);

        $sectionsCfg = array('configuration' => $this->_mapConfigurationFields($sectionsCfg));
        if ($variation) {
            $sectionsCfg = array_merge($sectionsCfg, array('variation' => $variation));
        }
        return $sectionsCfg;
    }

    /**
     * Update Pbridge configuration for specific merchant using Pbridge API
     *
     * @param array $magentoCfgSections
     * @param string $method
     * @param string $websiteCode
     * @param string $storeCode
     * @return Saas_Pbridge_Model_Pbridge_Merchant
     */
    protected function _updatePbridgeConfiguration(array $magentoCfgSections = array(), $method, $websiteCode = null,
        $storeCode = null)
    {
        if (!isset($magentoCfgSections['configuration'])) {
            $sectionsCfg = $this->_prepareCfgSections($magentoCfgSections, $websiteCode, $storeCode);
        } else {
            $sectionsCfg = $magentoCfgSections;
        }

        /* @var $api Saas_Pbridge_Model_Pbridge_Merchant_Api */
        $api = Mage::getModel('Saas_Pbridge_Model_Pbridge_Merchant_Api');

        switch ($method) {
            case self::METHOD_ALL_PAYMENTS:
            case self::METHOD_WPP_DIRECT:
            case self::METHOD_WPP_PE_DIRECT:
            case self::METHOD_PAYFLOWPRO:
            case self::METHOD_AUTHORIZENET:
            case self::METHOD_OGONE_DIRECT:
            case self::METHOD_OGONE_DIRECT_DEBIT:
            case self::METHOD_SAGEPAY_DIRECT:
            case self::METHOD_PAYONE_GATE:
            case self::METHOD_PAYONE_DEBIT:
            case self::METHOD_DIBS:
            case self::METHOD_PSIGATE_BASIC:
            case self::METHOD_PAYBOX_DIRECT:
            case self::METHOD_CYBERSOURCE:
            case self::METHOD_EWAY:
            case self::METHOD_BRAINTREE_BASIC:
            case self::METHOD_WORLDPAY_DIRECT:
            case self::METHOD_FIRSTDATA:
                $api->doUpdatePaymentsConfiguration($sectionsCfg);
                break;
            case self::METHOD_ALL_SERVICES:
            case self::METHOD_CENTINEL:
            case self::METHOD_KOUNT:
                $api->doUpdatePaymentServicesConfiguration($sectionsCfg);
                break;
            default:
                Mage::throwException(Mage::helper('Saas_Pbridge_Helper_Data')->__("Method is not present."));
                break;
        }

        return $this;
    }

    /**
     * Update Payment Profiles functionality switcher
     * @return Saas_Pbridge_Model_Pbridge_Merchant
     */
    protected function _updatePaymentProfileStatus()
    {
        $dependencies = array(
            array('payment/authorizenet/active', 'payment/authorizenet/payment_profiles_enabled'),
            array('payment/braintree_basic/active', 'payment/braintree_basic/payment_profiles_enabled')
        );
        $profileStatus = 0;
        foreach ($dependencies as $item) {
            if (is_array($item) && Mage::getStoreConfigFlag($item[0]) && Mage::getStoreConfigFlag($item[1])) {
                $profileStatus = 1;
                break;
            } else if (!is_array($item) && Mage::getStoreConfigFlag($item)) {
                $profileStatus = 1;
                break;
            }
        }
        Mage::getConfig()->saveConfig('payment/pbridge/profilestatus', $profileStatus);
        return $this;
    }

    /**
     * Fetch and prepare configuration array for update on Pbridge side
     *
     * @param array $sections
     * @param string $websiteCode
     * @param string $storeCode
     * @return array
     */
    protected function _fetchSectionsConfiguration(array $sections = array(), $websiteCode = null, $storeCode = null)
    {
        /* @var @config Mage_Core_Model_Config */
        $config    = Mage::app()->getConfig();
        $scope     = 'default';
        $scopeCode = null;
        if ($websiteCode && !$storeCode) {
            $scope     = 'website';
            $scopeCode = $websiteCode;
        } elseif ($websiteCode && $storeCode) {
            $scope     = 'store';
            $scopeCode = $storeCode;
        }
        $sectionsCfg = array();
        foreach ($sections as $code => $paths) {
            foreach ($paths as $path) {
                $paymentCfg = $config->getNode($path, $scope, $scopeCode);
                if ($paymentCfg) {
                    $tmpPaymentCfg = array();
                    foreach ($paymentCfg->children() as $nodeName => $node) {
                        $value = (string)$node;
                        if (!empty($node['backend_model']) && !empty($value)) {
                            $backend = Mage::getModel((string)$node['backend_model']);
                            $backend->setPath($path . '/' . $nodeName)->setValue($value)->afterLoad();
                            $value = $backend->getValue();
                        }
                        $tmpPaymentCfg[$nodeName] = $value;
                    }
                    if (!isset($sectionsCfg[$code])) {
                        $sectionsCfg[$code] = array();
                    }
                    $sectionsCfg[$code] = array_merge($sectionsCfg[$code], $tmpPaymentCfg);
                }
            }
        }
        return $sectionsCfg;
    }

    /**
     * Prepare variation of configuration depending on website code and merchant code
     *
     * @param string $websiteCode
     * @param string $storeCode
     * @return string|null
     */
    protected function _prepareVariation($websiteCode = null, $storeCode = null)
    {
        $variation = null;
//        if ($websiteCode && !$storeCode) {
//            $variation = Mage::app()->getConfig()
//                ->getNode('default/payment/pbridge/merchantcode') . '_' . $websiteCode;
//        }
        return $variation;
    }

    /**
     * Map Magento configuration fields to Pbridge configuration  fields.
     * Execute handlers for Pbridge fields if specified in Map Array
     *
     * @param array $configuration
     * @return array
     */
    protected function _mapConfigurationFields(array $configuration = array())
    {
        $mappedCfg = array();
        foreach ($this->_configurationMap as $code => $cfgFields) {
            $paymentRequest = array();
            if (isset($configuration[$code])) {
                foreach ($cfgFields as $pbridgeField => $magentoField) {
                    $value = null;
                    if (is_array($magentoField)) {
                        $valueHandler = $magentoField['handler'];
                        $value = $this->$valueHandler($configuration[$code]);
                    } elseif (isset($configuration[$code][$magentoField])) {
                        $value = isset($configuration[$code][$magentoField])
                            ? $configuration[$code][$magentoField]
                            : null;
                    }
                    $paymentRequest[$pbridgeField] = $value;
                }
                $mappedCfg[$code] = $paymentRequest;
            }
        }
        return $mappedCfg;
    }

    /**
     * Handler to prepare Verisign gateway URL
     *
     * @param array $paymentCgf
     * @return string
     */
    protected function _prepareVerisignGatewayUrl($paymentCgf)
    {
        if (!isset($paymentCgf['sandbox_flag'])) {
            return null;
        }
        if ($paymentCgf['sandbox_flag'] == 1) {
            $value = Mage_Paypal_Model_Payflowpro::TRANSACTION_URL_TEST_MODE;
        } else {
            $value = Mage_Paypal_Model_Payflowpro::TRANSACTION_URL;
        }
        return $value;
    }

    /**
     * Prepare Centinel account password (decrypting)
     *
     * @param array $serviceCgf
     * @return string
     */
    protected function _prepareCentinelPassword($serviceCgf)
    {
        if (!isset($serviceCgf['password'])) {
            return null;
        }
        return Mage::helper('Mage_Core_Helper_Data')->decrypt($serviceCgf['password']);
    }

    /**
     * Prepare data for boarding account
     *
     * @param array $serviceCgf
     * @return string
     */
    protected function _getBoardingAccount($serviceCgf)
    {
        if (!empty($serviceCgf['receiver_id'])) {
            return $serviceCgf['receiver_id'];
        }
        return $serviceCgf['boarding_account'];
    }
}
