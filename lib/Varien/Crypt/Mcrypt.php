<?php

/**
 * Mcrypt plugin
 *
 * @copyright   2007 Varien Inc.
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Varien
 * @subpackage  Crypt
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Varien_Crypt_Mcrypt extends Varien_Crypt_Abstract
{
    /**
     * Constuctor
     *
     * @param array $data
     */
    public function __construct(array $data=array())
    {
        parent::__construct($data);
    }
    
    /**
     * Initialize mcrypt module
     *
     * @param string $key cipher private key
     * @return Varien_Crypt_Mcrypt
     */
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
    
    /**
     * Encrypt data
     *
     * @param string $data source string
     * @return string
     */
    public function encrypt($data)
    {
        if (!$this->getHandler()) {
            throw new Varien_Exception("Crypt module is not initialized.");
        }
        return mcrypt_generic($this->getHandler(), $data);        
    }
    
    /**
     * Decrypt data
     *
     * @param string $data encrypted string
     * @return string
     */
    public function decrypt($data)
    {
        if (!$this->getHandler()) {
            throw new Varien_Exception("Crypt module is not initialized.");
        }
        return mdecrypt_generic($this->getHandler(), $data);        
    }
    
    /**
     * Desctruct cipher module
     *
     */
    public function __destruct()
    {
        mcrypt_generic_deinit($this->getHandler());
        mcrypt_module_close($this->getHandler());        
    }
}