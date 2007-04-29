<?php

/**
 * Possible data fields:
 * 
 * - subject
 * - to
 * - from
 * - body
 * - template (file name)
 * - module (for template)
 *
 */
class Mage_Core_Model_Email extends Varien_Data_Object
{
    public function send()
    {
        $to = $this->getTo();
        $headers = "From: ".$this->getFrom();
        $subject = $this->getSubject();
        
        $body = $this->getBody();
        if (empty($body) && $this->getTemplate()) {
            $block = Mage::getModel('core', 'layout')->createBlock('tpl', 'email')->setTemplate($this->getTemplate());
            $body = $block->toHtml();
        }
        
        $this->setResult(mail($to, $subject, $body, $headers));
        
        return $this;
    }
}