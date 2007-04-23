<?php

class Mage_Customer_Model_Payment extends Varien_Data_Object
{
    public function encryptCcNumber()
    {
        $this->setCcLast4(substr($this->getCcNumber(),-4));
        $this->setCcNumber($this->encrypt($this->getCcNumber()));
    }
    
    public function encrypt($data)
    {
        $key = (string)Mage::getConfig()->getModule('Mage_Customer')->crypt->key;
        return base64_encode(Varien_Crypt::factory()->init($key)->encrypt($data));
    }
    
    /**
     * Customer credit card decryption 
     *
     * @todo find out why it appends extra symbols if not using trim()
     * @param string $data
     * @return string
     */
    public function decrypt($data)
    {
        $key = (string)Mage::getConfig()->getModule('Mage_Customer')->crypt->key;
        return trim(Varien_Crypt::factory()->init($key)->decrypt(base64_decode($data)));
    }
}