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
 * @package    Mage_Log
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Log_Model_Visitor extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('log/visitor');
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Core_Model_Session_Abstract
     */
    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }
    
    /**
     * Retrieve visitor resource model
     *
     * @return mixed
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('log/visitor');
    }
    
    /**
     * Initialize visitor information from server data
     *
     * @return Mage_Log_Model_Visitor
     */
    public function initServerData()
    {
        $s = $_SERVER;
        $this->addData(array(
            'server_addr'       => empty($s['SERVER_ADDR']) ? '' : ip2long($s['SERVER_ADDR']),
            'remote_addr'       => empty($s['REMOTE_ADDR']) ? '' : ip2long($s['REMOTE_ADDR']),
            'http_secure'       => (int) !empty($s['HTTPS']),
            'http_host'         => empty($s['HTTP_HOST']) ? '' : $s['HTTP_HOST'],
            'http_user_agent'   => empty($s['HTTP_USER_AGENT']) ? '' : $s['HTTP_USER_AGENT'],
            'http_accept_language'=> empty($s['HTTP_ACCEPT_LANGUAGE']) ? '' : $s['HTTP_ACCEPT_LANGUAGE'],
            'http_accept_charset'=> empty($s['HTTP_ACCEPT_CHARSET']) ? '' : $s['HTTP_ACCEPT_CHARSET'],
            'request_uri'       => empty($s['REQUEST_URI']) ? '' : $s['REQUEST_URI'],
            'session_id'        => $this->_getSession()->getSessionId(),
            'http_referer'      => empty($s['HTTP_REFERER']) ? '' : $s['HTTP_REFERER'],
        ));

        return $this;
    }
    
    /**
     * Retrieve url from model data
     *
     * @return string
     */
    public function getUrl()
    {
        $url = 'http' . ($this->getHttpSecure() ? 's' : '') . '://';
        $url .= $this->getHttpHost().$this->getRequestUri();
        return $url;
    }
    
    public function isModuleIgnored($observer)
    {
        $ignores = Mage::getConfig()->getNode('global/ignoredModules/entities')->asArray();

        if( is_array($ignores) && $observer) {
            $curModule = $observer->getEvent()->getControllerAction()->getRequest()->getModuleName();
            if (isset($ignores[$curModule])) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Initialization visitor information by request
     * 
     * Used in event "controller_action_predispatch"
     * 
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Log_Model_Visitor
     */
    public function initByRequest($observer)
    {
        if ($this->isModuleIgnored($observer)) {
            return $this;
        }
        
        $this->setData($this->_getSession()->getVisitorData());
        $this->initServerData();
        
        if (!$this->getId()) {
            $this->setFirstVisitAt(now());
            $this->setIsNewVisitor(true);
            $this->save();
        }
        return $this;
    }
    
    /**
     * Saving visitor information by request
     * 
     * Used in event "controller_action_postdispatch"
     * 
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Log_Model_Visitor
     */
    public function saveByRequest($observer)
    {
        if ($this->isModuleIgnored($observer)) {
            return $this;
        }
        
        $this->setLastVisitAt(now());
        $this->save();
        
        $this->_getSession()->setVisitorData($this->getData());
        return $this;
    }
    
    /**
     * Bind customer data when customer login
     * 
     * Used in event "customer_login"
     * 
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Log_Model_Visitor
     */
    public function bindCustomerLogin($observer)
    {
        if (!$this->getCustomerId() && $customer = $observer->getEvent()->getCustomer()) {
            $this->setCustomerId($customer->getId());
        }
        return $this;
    }

    public function bindCustomerLogout($observer)
    {
        if ($this->getCustomerId() && $customer = $observer->getEvent()->getCustomer()) {
            $this->setIsCustomerLogout(true);
        }
        return $this;
    }

    public function bindQuoteCreate($observer)
    {
        /*$quoteId = $observer->getEvent()->getQuote()->getQuoteId();
        if( $quoteId ) {
            $this->setQuoteId($quoteId);
            $this->setQuoteCreatedAt($this->getResource()->getNow());
            $this->getResource()->logQuote($this);
        }*/

        return $this;
    }

    public function bindQuoteDestroy($observer)
    {
        /*$quoteId = $observer->getEvent()->getQuote()->getQuoteId();
        if( $quoteId ) {
            $this->setQuoteId($quoteId);
            $this->setQuoteDeletedAt($this->getResource()->getNow());
            $this->getResource()->logQuote($this);
        }*/

        return $this;
    }
    
    /**
     * Methods for research (depends from customer online admin section)
     */
    public function addIpData($data)
    {
        $ipData = array();
        $data->setIpData($ipData);
        return $this;
    }
    
    public function addCustomerData($data)
    {
        $customerId = $data->getCustomerId();
        if( intval($customerId) <= 0 ) {
            return $this;
        }
        $customerData = Mage::getModel('customer/customer')->load($customerId);
        $newCustomerData = array();
        foreach( $customerData->getData() as $propName => $propValue ) {
            $newCustomerData['customer_' . $propName] = $propValue;
        }

        $data->addData($newCustomerData);
        return $this;
    }

    public function addQuoteData($data)
    {
        $quoteId = $data->getQuoteId();
        if( intval($quoteId) <= 0 ) {
            return $this;
        }
        $data->setQuoteData(Mage::getModel('sales/quote')->load($quoteId));
        return $this;
    }
}