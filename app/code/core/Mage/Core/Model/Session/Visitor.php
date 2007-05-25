<?php

class Mage_Core_Model_Session_Visitor extends Varien_Object 
{
    public function getResource()
    {
        return Mage::getModel('core_resource', 'session_visitor');
    }
    
    public function collectBrowserData()
    {
        $s = $_SERVER;
        $this->addData(array(
            'server_addr'=>!empty($s['SERVER_ADDR']) ? $s['SERVER_ADDR'] : '',
            'remote_addr'=>!empty($s['REMOTE_ADDR']) ? $s['REMOTE_ADDR'] : '',
            'http_referer'=>!empty($s['HTTP_REFERER']) ? $s['HTTP_REFERER'] : '',
            'http_secure'=>!empty($s['HTTPS']),
            'http_host'=>!empty($s['HTTP_HOST']) ? $s['HTTP_HOST'] : '',
            'http_user_agent'=>!empty($s['HTTP_USER_AGENT']) ? $s['HTTP_USER_AGENT'] : '',
            'http_accept_language'=>!empty($s['HTTP_ACCEPT_LANGUAGE']) ? $s['HTTP_ACCEPT_LANGUAGE'] : '',
            'http_accept_charset'=>!empty($s['HTTP_ACCEPT_CHARSET']) ? $s['HTTP_ACCEPT_CHARSET'] : '',
            'request_uri'=>!empty($s['REQUEST_URI']) ? $s['REQUEST_URI'] : '',
        ));
        
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
    
    public function save()
    {
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
}