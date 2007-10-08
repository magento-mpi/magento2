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


class Mage_Log_Model_Visitor extends Varien_Object
{
    protected $_resource;

    public function getResource()
    {
        if( !$this->_resource ) {
            $this->_resource = Mage::getResourceModel('log/visitor');
        }
        return $this->_resource;
    }

    public function collectBrowserData()
    {
        $s = $_SERVER;
        $this->addData(array(
            'server_addr'=>!empty($s['SERVER_ADDR']) ? ip2long($s['SERVER_ADDR']) : '',
            'remote_addr'=>!empty($s['REMOTE_ADDR']) ? ip2long($s['REMOTE_ADDR']) : '',
            'http_secure'=>(int) !empty($s['HTTPS']),
            'http_host'=>!empty($s['HTTP_HOST']) ? $s['HTTP_HOST'] : '',
            'http_user_agent'=>!empty($s['HTTP_USER_AGENT']) ? $s['HTTP_USER_AGENT'] : '',
            'http_accept_language'=>!empty($s['HTTP_ACCEPT_LANGUAGE']) ? $s['HTTP_ACCEPT_LANGUAGE'] : '',
            'http_accept_charset'=>!empty($s['HTTP_ACCEPT_CHARSET']) ? $s['HTTP_ACCEPT_CHARSET'] : '',
            'request_uri'=>!empty($s['REQUEST_URI']) ? $s['REQUEST_URI'] : '',
        ));

        if ($this->getFirstVisitAt()==$this->getLastVisitAt() && !empty($s['HTTP_REFERER'])) {
            $this->setHttpReferer($s['HTTP_REFERER']);
        }

        return $this;
    }

    public function getUrl()
    {
        $url = 'http' . ($this->getHttpSecure() ? 's' : '') . '://';
        $url .= $this->getHttpHost().$this->getRequestUri();
        return $url;
    }

    public function load()
    {
        $session = Mage::getSingleton('core/session');
        $now = $this->getResource()->getNow();

        $data = $this->getResource()->load($session->getLogVisitorId());
        if ($data) {
            $this->setData($data);
        } else {
            $this->setSessionId($session->getSessionId());
            $this->setFirstVisitAt($now);
        }
        $this->setLastVisitAt($now);

        $this->collectBrowserData();

        return $this;
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

    public function loadByAction($observer)
    {
        $this->load(Mage::getSingleton('core/session')->getSessionId());
        return $this;
    }


    public function save($observer = null)
    {
        if ($this->isModuleIgnored($observer)) {
            return $this;
        }

        $this->setResourceVisitorId();

        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        #if( empty($customerId) ) {
        #    $this->setLogoutNeeded(1);
        #}

        #$this->getResource()
        #    ->logVisitor($this);
        #    ->logUrl($this);

        Mage::getSingleton('core/session')->setLogVisitorId($this->getVisitorId());

        return $this;
    }

    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }

    /**
     * Bind checkout session qouote id
     *
     * Used in event "initCheckoutSession" observer
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Log_Model_Visitor
     */
    public function bindCheckoutSession($observer)
    {
        if ($observer->getEvent()->getCheckoutSession()) {
            $this->setQuoteId($observer->getEvent()->getCheckoutSession()->getQuoteId());
        }
        return $this;
    }

    public function bindStore($observer)
    {
        if ($store = $observer->getEvent()->getStore()) {
            $this->setStoreId($store->getId());
        }
    }

    public function bindCustomer($observer)
    {
        $session = $observer->getEvent()->getCustomerSession();
        $isLoggedIn = $session && $session->isLoggedIn();
        return;
        if ($isLoggedIn) {
            $this->setCustomerId($observer->getEvent()->getCustomerSession()->getCustomerId());
        }
    }

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

    public function bindCustomerLogin()
    {
        $this->setResourceVisitorId();
        $session = Mage::getSingleton('customer/session');
        $this->setLoginAt( $this->getResource()->getNow() );
        $this->setCustomerId( $session->getCustomerId() );
        $this->getResource()
            ->logCustomer($this);

        return $this;
    }

    public function bindCustomerLogout($observer)
    {
        $this->setResourceVisitorId();
        $eventData = $observer->getEvent()->getData();
        $this->setLogoutAt( $this->getResource()->getNow() );
        $this->setCustomerId( $eventData['customer']->getCustomerId() );
        $this->getResource()->logCustomer($this);
        Mage::getSingleton('core/session')->setLogVisitorId(null);

        return $this;
    }

    public function bindQuoteCreate($observer)
    {
        $this->setResourceVisitorId();
        $quoteId = $observer->getEvent()->getQuote()->getQuoteId();
        if( $quoteId ) {
            $this->setQuoteId($quoteId);
            $this->setQuoteCreatedAt($this->getResource()->getNow());
            $this->getResource()->logQuote($this);
        }

        return $this;
    }

    public function bindQuoteDestroy($observer)
    {
        $this->setResourceVisitorId();
        $quoteId = $observer->getEvent()->getQuote()->getQuoteId();
        if( $quoteId ) {
            $this->setQuoteId($quoteId);
            $this->setQuoteDeletedAt($this->getResource()->getNow());
            $this->getResource()->logQuote($this);
        }

        return $this;
    }

    public function setResourceVisitorId()
    {
        $session = Mage::getSingleton('core/session');
        if( intval($session->getLogVisitorId()) > 0 ) {
            $this->setSessionVisitorId($session->getLogVisitorId());
            $this->setVisitorId($session->getLogVisitorId());
        } else {
            $session->setLogVisitorId($this->getVisitorId());
        }
    }
}