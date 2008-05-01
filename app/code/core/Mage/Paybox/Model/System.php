<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Paybox
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paybox System Model
 *
 * @category   Mage
 * @package    Mage_Paybox
 * @author     Ruslan Voitenko <ruslan.voytenko@varien.com>
 */
class Mage_Paybox_Model_System extends Mage_Payment_Model_Method_Abstract
{
    const PBX_FORM_HTML_METHOD    = 1;
    const PBX_COMMAND_LINE_METHOD = 4;

    const PBX_METHOD_CALL = 'POST';

    const PBX_PAYMENT_ACTION_ATHORIZE = 'O';
    const PBX_PAYMENT_ACTION_ATHORIZE_CAPTURE = 'N';

    const PBX_PAYMENT_TYPE_CARTE    = 'CARTE';
    const PBX_PAYMENT_TYPE_SYMPASS  = 'SYMPASS';
    const PBX_PAYMENT_TYPE_PAYNOVA  = 'PAYNOVA';
    const PBX_PAYMENT_TYPE_TERMINEO = 'TERMINEO';
    const PBX_PAYMENT_TYPE_PAYPAL   = 'PAYPAL';
    const PBX_PAYMENT_TYPE_UNEURO   = 'UNEURO';

    const PBX_CARTE_TYPE_CB                 = 'CB';
    const PBX_CARTE_TYPE_VISA               = 'VISA';
    const PBX_CARTE_TYPE_EUROCARDMASTERCARD = 'EUROCARD_MASTERCARD';
    const PBX_CARTE_TYPE_ECARD              = 'E_CARD';
    const PBX_CARTE_TYPE_AMEX               = 'AMEX';
    const PBX_CARTE_TYPE_DINERS             = 'DINERS';
    const PBX_CARTE_TYPE_JCB                = 'JCB';
    const PBX_CARTE_TYPE_AURORE             = 'AURORE';
    const PBX_CARTE_TYPE_COFINOGA           = 'COFINOGA';
    const PBX_CARTE_TYPE_SOFINCO            = 'SOFINCO';
    const PBX_CARTE_TYPE_PAYNOVA            = 'PAYNOVA';
    const PBX_CARTE_TYPE_TERMINEO           = 'TERMINEO';
    const PBX_CARTE_TYPE_PAYPAL             = 'PAYPAL';
    const PBX_CARTE_TYPE_UNEURO             = 'UNEURO';

    protected $_code  = 'paybox_system';

    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = false;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_formBlockType = 'paybox/system_form';

    protected $_order;
    protected $_carteTypes;
    protected $_currenciesNumbers;

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')
                            ->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
        }
        return $this->_order;
    }

    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
    }

    protected function _getCarteTypes($paymentType = null)
    {
        if (!$this->_carteTypes) {
            $this->_carteTypes = array(
                self::PBX_PAYMENT_TYPE_CARTE => array(
                    'none' => Mage::helper('paybox')->__('Customer Choise'),
                    self::PBX_CARTE_TYPE_CB => Mage::helper('paybox')->__('CB'),
                    self::PBX_CARTE_TYPE_VISA => Mage::helper('paybox')->__('VISA'),
                    self::PBX_CARTE_TYPE_EUROCARDMASTERCARD => Mage::helper('paybox')->__('EUROCARD & MASTERCARD'),
                    self::PBX_CARTE_TYPE_ECARD => Mage::helper('paybox')->__('E CARD'),
                    self::PBX_CARTE_TYPE_AMEX => Mage::helper('paybox')->__('AMEX'),
                    self::PBX_CARTE_TYPE_DINERS => Mage::helper('paybox')->__('DINERS'),
                    self::PBX_CARTE_TYPE_JCB => Mage::helper('paybox')->__('JCB'),
                    self::PBX_CARTE_TYPE_COFINOGA => Mage::helper('paybox')->__('COFINOGA'),
                    self::PBX_CARTE_TYPE_SOFINCO => Mage::helper('paybox')->__('SOFINCO'),
                    self::PBX_CARTE_TYPE_AURORE => Mage::helper('paybox')->__('AURORE'),
                ),
                self::PBX_PAYMENT_TYPE_SYMPASS => array(
                    'none' => Mage::helper('paybox')->__('Customer Choise'),
                    self::PBX_CARTE_TYPE_CB => Mage::helper('paybox')->__('CB'),
                    self::PBX_CARTE_TYPE_VISA => Mage::helper('paybox')->__('VISA'),
                    self::PBX_CARTE_TYPE_EUROCARDMASTERCARD => Mage::helper('paybox')->__('EUROCARD & MASTERCARD'),
                    self::PBX_CARTE_TYPE_ECARD => Mage::helper('paybox')->__('E CARD'),
                    self::PBX_CARTE_TYPE_AMEX => Mage::helper('paybox')->__('AMEX'),
                    self::PBX_CARTE_TYPE_DINERS => Mage::helper('paybox')->__('DINERS'),
                    self::PBX_CARTE_TYPE_JCB => Mage::helper('paybox')->__('JCB'),
                    self::PBX_CARTE_TYPE_AURORE => Mage::helper('paybox')->__('AURORE'),
                ),
                self::PBX_PAYMENT_TYPE_PAYNOVA => array(
                    self::PBX_CARTE_TYPE_PAYNOVA => Mage::helper('paybox')->__('PAYNOVA'),
                ),
                self::PBX_PAYMENT_TYPE_TERMINEO => array(
                    self::PBX_CARTE_TYPE_TERMINEO => Mage::helper('paybox')->__('TERMINEO'),
                ),
                self::PBX_PAYMENT_TYPE_PAYPAL => array(
                    self::PBX_CARTE_TYPE_PAYPAL => Mage::helper('paybox')->__('PAYPAL'),
                ),
                self::PBX_PAYMENT_TYPE_UNEURO => array(
                  self::PBX_CARTE_TYPE_UNEURO => Mage::helper('paybox')->__('UNEURO'),
                )
            );
        }

        if (!is_null($paymentType)) {
            if (isset($this->_carteTypes[$paymentType])) {
                return $this->_carteTypes[$paymentType];
            }
        }

        return $this->_carteTypes;
    }

    public function getCarteTypesByPayment($paymentType)
    {
        if ($paymentType == '') {
            return array();
        }
        return $this->_getCarteTypes($paymentType);
    }

    public function getJsonCarteTypes()
    {
        return Zend_Json::encode($this->_getCarteTypes());
    }

    public function getPaymentMethod()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_mode');
    }

    public function getPayboxFile()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_file');
    }

    public function getPaymentType()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_typepaiement');
    }

    /**
     * Get Payment Action of Paybox System changed to Paybox specification
     *
     * @return string
     */
    public function getPaymentAction()
    {
        $paymentAction = Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_autoseule');
        switch ($paymentAction) {
            case self::ACTION_AUTHORIZE:
                return self::PBX_PAYMENT_ACTION_ATHORIZE;
                break;
            case self::ACTION_AUTHORIZE_CAPTURE:
                return self::PBX_PAYMENT_ACTION_ATHORIZE_CAPTURE;
                break;
            default:
                return self::PBX_PAYMENT_ACTION_ATHORIZE;
                break;
        }
    }

    public function getCarteType()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_typecarte');
    }

    public function getSiteNumber()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_site');
    }

    public function getRang()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_rang');
    }

    public function getIdentifiant()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_identifiant');
    }

    public function getCurrencyNumb()
    {
        $currencyCode = $this->getOrder()->getBaseCurrencyCode();
        if (!$this->_currenciesNumbers) {
            $this->_currenciesNumbers = simplexml_load_file(Mage::getBaseDir().'/app/code/core/Mage/Paybox/etc/currency.xml');
        }
        if ($this->_currenciesNumbers->$currencyCode) {
            return $this->_currenciesNumbers->$currencyCode;
        }
    }

    public function getLanguage()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_langue');
    }

    public function getCodeFamille()
    {
        return Mage::getStoreConfig('paybox' . $this->getCode() . 'api/pbx_codefamille');
    }

    public function getUneuroCode()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_1euro_codeexterne');
    }

    public function getCofidisCode()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_1euro_codecofidis');
    }

    public function getUneuroData()
    {
        /*array(
            'Civility',
            'Last name',
            'First name',
            'Address 1',
            'Address 2',
            'Address 3',
            'Zip code',
            'City',
            'Country code',
            'Telephone number',
            'Telephone mobile',
            '1 if knows customer',
            '1 if chargebacks',
            'Action code for COFIDIS'
        );*/
/*
{{var company}} {{var street1}} {{var street2}} {{var region}}
{{depend fax}}F: {{var fax}}{{/depend}}
*/

        $billing = $this->getOrder()->getBillingAddress();
        $customerType = ($this->getOrder()->getCustomerId()?'1':'0');

        $formatedAddress = ($billing->getCompany()?$billing->getCompany() . ' ':'') . $billing->getStreet(1) . ' ' .
                            ($billing->getStreet(2)?$billing->getStreet(2) . ' ':'') . $billing->getRegion() .
                            ($billing->getFax()?' ' . $billing->getFax():'');

        $uneuroData = '' . '#' . $billing->getLastname() . '#' . $billing->getFirstname() . '#' . $formatedAddress . '###' .
                        $billing->getPostcode() . '#' . $billing->getCity() . '#' . $billing->getCountry() . '#' .
                        $billing->getTelephone() . '##' . $customerType . '#' . '0' . '#' . $this->getCofidisCode() . '#';

        return $uneuroData;
    }

    public function getApiUrls()
    {
        $fielldsArr = array();
        if (($primary = trim(Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_paybox'))) != '') {
            $fielldsArr['PBX_PAYBOX'] = $primary;
        }

        if (($backup1 = trim(Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_backup1'))) != '') {
            $fielldsArr['PBX_BACKUP1'] = $backup1;
        }

        if (($backup2 = trim(Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_backup2'))) != '') {
            $fielldsArr['PBX_BACKUP2'] = $backup2;
        }

        if (($backup3 = trim(Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_backup3'))) != '') {
            $fielldsArr['PBX_BACKUP3'] = $backup3;
        }

        return $fielldsArr;
    }

    public function getTimeouts()
    {
        $fielldsArr = array();
        if (($timeout = trim(Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_timeout'))) != '') {
            $fielldsArr['PBX_TIMEOUT'] = $timeout;
        }

        if (($timeout1 = trim(Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_timeout1'))) != '') {
            $fielldsArr['PBX_TIMEOUT1'] = $timeout1;
        }

        if (($timeout2 = trim(Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_timeout2'))) != '') {
            $fielldsArr['PBX_TIMEOUT2'] = $timeout2;
        }

        if (($timeout3 = trim(Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_timeout3'))) != '') {
            $fielldsArr['PBX_TIMEOUT3'] = $timeout3;
        }

        return $fielldsArr;
    }

    public function getManagementMode()
    {
        $fieldsArr = array();
        if (($text = trim(Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_txt'))) != '') {
            $fieldsArr['PBX_TXT'] = $text;
        }

        if (($wait = trim(Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_wait'))) != '') {
            $fieldsArr['PBX_WAIT'] = $wait;
        }

        if (($boutpi = trim(Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_boutpi')))) {
            $fieldsArr['PBX_BOUTPI'] = $boutpi;
        }

        if (($bkgd = trim(Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_bkgd'))) != '') {
            $fieldsArr['PBX_BKGD'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'payment/paybox/bkgd/' . $bkgd;
        }

        $fieldsArr['PBX_OUTPUT'] = Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_output');

        return $fieldsArr;
    }

    public function getPingFlag()
    {
        return Mage::getStoreConfigFlag('paybox/' . $this->getCode() . 'api/pbx_ping');
    }

    public function getPingPort()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_port');
    }

    public function getDebugFlag()
    {
        return Mage::getStoreConfigFlag('paybox/' . $this->getCode() . 'api/debug_flag');
    }

    public function getOrderPlaceRedirectUrl()
    {
        if ($this->getPaymentMethod() == self::PBX_FORM_HTML_METHOD) {
            return Mage::getUrl('paybox/system/redirect', array('_secure' => true));
        } else {
            return Mage::getUrl('paybox/system/commandline', array('_secure' => true));
        }
    }

    public function getFormFields()
    {
        $fieldsArr = array();

        $fieldsArr = array(
            'PBX_MODE' => $this->getPaymentMethod(),
            'PBX_SITE' => $this->getSiteNumber(),//'1999888',
            'PBX_RANG' => $this->getRang(),//'99',
            'PBX_IDENTIFIANT' => $this->getIdentifiant(),//'2',
            'PBX_TOTAL' => ($this->getOrder()->getBaseGrandTotal()*100),
            'PBX_DEVISE' => $this->getCurrencyNumb(),
            'PBX_CMD' => $this->getOrder()->getRealOrderId(),
            'PBX_PORTEUR' => $this->getOrder()->getCustomerEmail(),
            'PBX_RETOUR' => 'amount:M;ref:R;auto:A;trans:T;error:E',
            'PBX_EFFECTUE' => Mage::getUrl('paybox/system/success', array('_secure' => true)),
            'PBX_REFUSE' => Mage::getUrl('paybox/system/refuse', array('_secure' => true)),
            'PBX_ANNULE' => Mage::getUrl('paybox/system/decline', array('_secure' => true)),
            'PBX_AUTOSEULE' => $this->getPaymentAction(),
            'PBX_LANGUE' => $this->getLanguage(),
            'PBX_ERREUR' => Mage::getUrl('paybox/system/error', array('_secure' => true)),
            'PBX_TYPEPAIEMENT' => $this->getPaymentType(),
            'PBX_TYPECARTE' => $this->getCarteType(),
            'PBX_RUF1' => self::PBX_METHOD_CALL,
        );

        if ($this->getCarteType == self::PBX_CARTE_TYPE_COFINOGA ||
            $this->getCarteType() == self::PBX_CARTE_TYPE_SOFINCO) {
            $fieldsArr = array_merge($fieldsArr, array(
                    'PBX_CODEFAMILLE' => $this->getCodeFamille()
                )
            );
        }

        if ($this->getcarteType() == self::PBX_CARTE_TYPE_UNEURO) {
            $fieldsArr = array_merge($fieldsArr, array(
                    'PBX_1EURO_CODEEXTERNE' => $this->getUneuroCode(),
                    'PBX_1EURO_DATA' => $this->getUneuroData()
                )
            );
        }

        if (count($apiUrls = $this->getApiUrls())) {
            $fieldsArr = array_merge($fieldsArr, $this->getApiUrls());
        }
        if (count($timeouts = $this->getTimeouts())) {
            $fieldsArr = array_merge($fieldsArr, $this->getTimeouts());
        }

        $fieldsArr = array_merge($fieldsArr, $this->getManagementMode());

        if ($this->getPaymentMethod() == self::PBX_COMMAND_LINE_METHOD && $this->getPingFlag()) {
            $tmpFieldsArr['PBX_PING'] = '1';
            if (($pingPort = trim($this->getPingPort())) != '') {
                $tmpFieldsArr['PING_PORT'] = $pingPort;
            }

            $fieldsArr = array_merge($fieldsArr, $tmpFieldsArr);
        }

        if ($this->getDebugFlag()) {
            $debug = Mage::getModel('paybox/api_debug')
                ->setRealOrderId($this->getOrder()->getRealOrderId())
                ->setRequestBody(print_r($fieldsArr, 1))
                ->save();
        }

        return $fieldsArr;
    }

    public function checkResponse($response)
    {
        if ($this->getDebugFlag()) {
            $debug = Mage::getModel('paybox/api_debug')
                ->load($response['ref'], 'real_order_id')
                ->setResponseBody(print_r($response, 1))
                ->save();
        }

        if (isset($response['error'], $response['amount'],
            $response['ref'], $response['trans'])
            ) {
            return true;
        }
        return false;
    }

    public function validate()
    {
        return parent::validate();
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId());
        return $this;
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED);

        return $this;
    }
}