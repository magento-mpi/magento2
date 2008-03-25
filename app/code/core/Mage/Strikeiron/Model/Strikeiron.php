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
 * @package    Mage_Strikeiron
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sitemap model
 *
 * @category   Mage
 * @package    Mage_Strikeiron
 * @author     Lindy Kyaw <lindy@varien.com>
 */
class Mage_Strikeiron_Model_Strikeiron extends Mage_Core_Model_Abstract
{
    const EMAIL_UNDETERMINED_ACCEPT = 'Accept';
    const EMAIL_UNDETERMINED_REJECT = 'Reject';
    const EMAIL_UNDETERMINED_CONFIRM = 'Confirm';

    public function getForeignExchangeRatesApi()
    {
        return Mage::getSingleton('strikeiron/service_foreignExchangeRates', $this->getConfiguration());
    }

    public function getEmailVerificationApi()
    {
        return Mage::getSingleton('strikeiron/service_emailVerification', $this->getConfiguration());
    }

    protected function getConfiguration()
    {
        return array('username'=> $this->getConfigData('config', 'user') , 'password'=> $this->getConfigData('config', 'password'));
    }

    public function getConfigData($code, $field)
    {
        $path = 'strikeiron/'.$code.'/'.$field;
        return Mage::getStoreConfig($path);
    }

    /*
    verify email address is valid or not
    wsdl = http://ws.strikeiron.com/varien.StrikeIron/emailverify_3_0?WSDL
    */
    public function emailVerify($email)
    {
        if ($email && $this->getConfigData('email_verification', 'active')) {
            $_session = Mage::getSingleton('customer/session');
            /*
            * following flag will set if the email is undetermined for the first time
            * for second time, we just need to return true
            */
            if ($_session->getStrikeironUndertermined()==$email) {
               $_session->unsStrikeironUndertermined();
               return true;
            }

            $emailApi = $this->getEmailVerificationApi();

            $checkAllServer = $this->getConfigData('email_verification', 'check_allservers');
            $emailArr = array(
                'email' => $email,
                'checkAllServers' => ($checkAllServer ? 'True' : 'False')
            );
            try {
                $result = $emailApi->validateEmail($emailArr);
                if ($result) {
                    switch($result->IsValid){
                       case 'INVALID':
                           Mage::throwException(Mage::helper('strikeiron')->__('Invalid email address'));
                       break;
                       case 'UNDETERMINED':
                           switch($this->getConfigData('email_verification', 'undetermined_action')){
                               case self:: EMAIL_UNDETERMINED_REJECT:
                                   Mage::throwException(Mage::helper('strikeiron')->__('Invalid email address'));
                               break;
                               case  self::EMAIL_UNDETERMINED_CONFIRM:
                                      $_session->setStrikeironUndertermined($email);
                                      Mage::throwException(Mage::helper('strikeiron')->__('Email address cannot be verified. Please check again and make sure your email address entered correctly.'));
                               break;
                           }
                       break;
                   }
                } else {
                   Mage::throwException(Mage::helper('strikeiron')->__('There is no response back from Strikeiron server'));
                }
            } catch (Zend_Service_StrikeIron_Exception $e) {
                /*
                * when there is exception from Zend_Service_StrikeIron_Exception
                * we will send email to email recipient for exception
                */
                /* @var $mailTamplate Mage_Core_Model_Email_Template */
                $receipient = $this->getConfigData('email_verification', 'error_email');
                if ($receipient) {
                    $mailTamplate = Mage::getModel('core/email_template');
                    $mailTamplate->setDesignConfig(
                            array(
                                'area'  => 'frontend',
                            )
                        )
                        ->sendTransactional(
                            $this->getConfigData('email_verification', 'error_email_template'),
                            $this->getConfigData('email_verification', 'error_email_identity'),
                            $receipient,
                            null,
                            array(
                              'email'       => $email,
                              'warnings'    => $e->getMessage(),
                            )
                        );
                }
            }
        }
        return true;
    }


    public function _getAllSupportedCurrencies($exchangeApi)
    {
        $result = $exchangeApi->GetSupportedCurrencies();
        $data = array();
        if ($result && $result->ServiceStatus && $result->ServiceStatus->StatusNbr == 210) {
            $listings = $result->ServiceResult->Listings;
            if ($listings && $listings->CurrencyListing) {
                foreach($listings->CurrencyListing as $listing){
                    $data[] = $listing->Symbol;
                }
            }
        }
        return $data;
    }

    /*
    retrieving foreign exchange rate for the currency
    wsdl = http://ws.strikeiron.com/varien.StrikeIron/ForeignExchangeRate?WSDL
    */
    public function fetchExchangeRate ($defaultCurrency, $currencies=array())
    {
        if(!$this->getConfigData('currency', 'foreigh_xrate')){
            Mage::throwException(Mage::helper('strikeiron')->__('Strikeiron foreign exchange rate is disabled'));
        }

        $data = array();
        $exchangeApi = $this->getForeignExchangeRatesApi();
        $supportedCurrencies = $this->_getAllSupportedCurrencies($exchangeApi);
        if($supportedCurrencies) {
            $availableCurrencies = array_intersect($currencies, $supportedCurrencies);
            if($availableCurrencies && in_array($defaultCurrency,$supportedCurrencies)){
                $currenciesStr = implode(', ' , $availableCurrencies);
                $reqArr = array(
                    'CommaSeparatedListOfCurrenciesFrom' => $currenciesStr,
                    'SingleCurrencyTo' => $defaultCurrency
                );
                $result = $exchangeApi->GetLatestRates($reqArr);
                if ($result) {
                    /*
                    212 = Currency rate data Found
                    */
                    if ($result->ServiceStatus && $result->ServiceStatus->StatusNbr == 212) {
                      $listings = $result->ServiceResult->Listings;
                      if($listings && $listings->ExchangeRateListing) {
                          foreach ($listings->ExchangeRateListing as $listing) {
                              $data[$listing->PerCurrency][$listing->Currency] = $listing->Value;
                          }
                      }
                    } else {
                      Mage::throwException($result->ServiceStatus->StatusDescription);
                    }
                } else {
                   Mage::throwException(Mage::helper('strikeiron')->__('There is no response back from Strikeiron server'));
                }
            }
        }
        return $data;
    }

    public function customerSaveBeforeObserver($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $isAdmin = Mage::getDesign()->getArea()==='adminhtml';
        $email = $customer->getEmail();
        $host =  Mage::app()->getStore()->getConfig(Mage_Customer_Model_Customer::XML_PATH_DEFAULT_EMAIL_DOMAIN);
        $fakeEmail = $customer->getIncrementId().'@'. $host;
        if ($email && $email != $fakeEmail && $customer->dataHasChangedFor('email') && (!$isAdmin || ($isAdmin && $this->getConfigData('email_verification', 'check_admin')))) {
            $this->emailVerify($email);
        }
    }
}