<?php

class Varien_Crypt_Mcrypt extends Varien_Crypt_Abstract
{
    public function __construct($data=null)
    {
        parent::__construct($data);
    }
    
    public function init($key)
    {
        if (!$this->getCipher()) {
            $this->setCipher(MCRYPT_BLOWFISH);
        }
        
        if (!$this->getMode()) {
            $this->setMode(MCRYPT_MODE_ECB);
        }
        
        $this->setHandler(mcrypt_module_open($this->getCipher(), '', $this->getMode(), ''));
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($this->getHandler()), MCRYPT_RAND);
        mcrypt_generic_init($this->getHandler(), $key, $iv);
        
        return $this;
    }
    
    public function encrypt($data)
    {
        if (!$this->getHandler()) {
            throw new Varien_Exception("Crypt module is not initialized.");
        }
        return mcrypt_generic($this->getHandler(), $data);        
    }
    
    public function decrypt($data)
    {
        if (!$this->getHandler()) {
            throw new Varien_Exception("Crypt module is not initialized.");
        }
        return mdecrypt_generic($this->getHandler(), $data);        
    }
    
    public function __destruct()
    {
        mcrypt_generic_deinit($this->getHandler());
        mcrypt_module_close($this->getHandler());        
    }
}