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
    protected $_tplVars = array();
    
    public function __construct()
    {
        // TODO: move to config
        $this->setFromName('Magenta');
        $this->setFromEmail('magenta@varien.com');
    }
    
    public function setTemplateVar($var, $value = null)
    {
        if (is_array($var)) {
            foreach ($var as $index=>$value) {
                $this->_tplVars[$index] = $value;
            }
        }
        else {
            $this->_tplVars[$var] = $value;
        }
    }
    
    public function getTemplateVars()
    {
        return $this->_tplVars;
    }
    
    public function getBody()
    {
        $body = $this->getData('body');
        if (empty($body) && $this->getTemplate()) {
            $block = Mage::getModel('core', 'layout')->createBlock('tpl', 'email')->setTemplate($this->getTemplate());
            foreach ($this->getTemplateVars() as $var=>$value) {
                $block->assign($var, $value);
            }
            $body = $block->toHtml();
        }
        return $body;
    }
    
    public function send()
    {
        $mail = new Zend_Mail();
        
        $mail->setBodyText($this->getBody())
            ->setFrom($this->getFromEmail(), $this->getFromName())
            ->addTo($this->getToEmail(), $this->getToName())
            ->setSubject($this->getSubject());
        $mail->send();
        
        return $this;
    }
}