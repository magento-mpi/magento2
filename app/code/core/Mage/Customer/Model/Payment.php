<?php

class Mage_Customer_Model_Payment extends Varien_Data_Object
{
    public function setData($var, $value='', $isChanged=true)
    {
        if ('cc_number'===$var) {
            $this->setCcLast4(substr($value,-4));
            if (!empty($value)) {
                $this->setCcNumberEnc($this->encrypt($value));
            } else {
                $this->setCcNumberEnc('');
            }
        }
        return parent::setData($var, $value, $isChanged);
    }
    
    public function getData($key='', $index=false)
    {
        if ('cc_number'===$key) {
            if (empty($this->_data['cc_number']) && !empty($this->_data['cc_number_enc'])) {
                $this->_data['cc_number'] = $this->decrypt($this->getCcNumberEnc());
            }
        }
        return parent::getData($key, $index);
    }
    
    public function encrypt($data)
    {
        $key = (string)Mage::getConfig()->getNode('modules/Mage_Customer/crypt/key');
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
        $key = (string)Mage::getConfig()->getNode('modules/Mage_Customer/crypt/key');
        return trim(Varien_Crypt::factory()->init($key)->decrypt(base64_decode($data)));
    }
}