<?php

class Mage_Core_Controller_Zend_Request extends Zend_Controller_Request_Http 
{
    public function buildUrl()
    {
        $params = $this->getParams();
        
        $params['module'] = (string)Mage::getModule($params['module'])->controller->frontName;
        
        $url = 'http'.($this->getServer('HTTPS')?'s':'').'://'.$this->getServer('HTTP_HOST');
        $url .= $this->getBaseUrl() !== '/' ? $this->getBaseUrl() : '';
        $url .= '/'.$params['module'].'/'.$params['controller'].'/'.$params['action'];
        
        unset($params['module'], $params['controller'], $params['action']);
        
        foreach ($params as $key=>$value) {
            $url .= '/'.$key.'/'.$value;
        }
        
        return $url;
    }
}