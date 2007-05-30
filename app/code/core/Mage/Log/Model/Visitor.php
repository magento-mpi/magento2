<?php

class Mage_Log_Model_Visitor extends Varien_Object 
{
    public function getResource()
    {
        return Mage::getModel('log_resource', 'visitor');
    }
    
    public function collectBrowserData()
    {
        $s = $_SERVER;
        $this->addData(array(
            'server_addr'=>!empty($s['SERVER_ADDR']) ? $s['SERVER_ADDR'] : '',
            'remote_addr'=>!empty($s['REMOTE_ADDR']) ? $s['REMOTE_ADDR'] : '',
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
    
    public function load($sessId)
    {
        $now = date('Y-m-d H:i:s');
        $data = $this->getResource()->load($sessId);
        if ($data) {
            $this->setData($data);
        } else {
            $this->setSessionId($sessId);
            $this->setFirstVisitAt($now);
        }
        $this->setLastVisitAt($now);
        
        $this->collectBrowserData();

        return $this;
    }
    
    public function loadByAction($observer)
    {
        if ($observer->getEvent()->getControllerAction()->getRequest()->getModuleName()=='Mage_Install') {
            return $this;
        }
        $this->load(Mage::getSingleton('core', 'session')->getSessionId());
        return $this;
    }
    
    public function save($observer = null)
    {
        if ($observer && $observer->getEvent()->getControllerAction()->getRequest()->getModuleName()=='Mage_Install') {
            return $this;
        }
        
        $history = $this->getUrlHistory();
        $this->setUrlHistory((!empty($history) ? $history."\n" : '') . $this->getUrl());

        $this->getResource()->save($this);
        
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
    
    public function bindWebsite($observer)
    {
        if ($observer->getEvent()->getWebsite()) {
            $this->setWebsiteId($observer->getEvent()->getWebsite()->getId());
        }
    }
    
    public function bindCustomer($observer)
    {
        if ($observer->getEvent()->getCustomerSession() && $observer->getEvent()->getCustomerSession()->isLoggedIn()) {
            $this->setCustomerId($observer->getEvent()->getCustomerSession()->getCustomerId());
        }
    }
}