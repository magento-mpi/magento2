<?php
/**
 * Template model
 *
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Newsletter_Model_Template extends Varien_Object
{
    /**
     * Types of template
     */
    const TYPE_TEXT = 1;
    const TYPE_HTML = 2;

    /** 
     * Return resource of template model.
     *
     * @return Mage_Newsletter_Model_Mysql4_Template
     */
    public function getResource() 
    {
        return Mage::getResourceSingleton('newsletter/template');
    }
  
    /**
     * Load template by id
     * 
     * @param   int $templateId
     * return   Mage_Newsletter_Model_Template
     */
    public function load($templateId)
    {
        $this->addData($this->getResource()->load($templateId));
        return $this;
    }
    
    /**
     * Load template by code
     * 
     * @param   string $templateCode
     * return   Mage_Newsletter_Model_Template
     */
    public function loadByCode($templateCode)
    {
        $this->addData($this->getResource()->loadByCode($templateCode));
        return $this;
    }
    
    /**
     * Return template id
     * return int|null
     */
    public function getId() 
    {
        return $this->getTemplateId();
    }
    
    /**
     * Set id of template
     * @param int $value 
     */
    public function setId($value)
    {
        return $this->setTemplateId($value);
    }
    
    /**
     * Return true if this template can be used for sending queue as main template
     *
     * @return boolean
     */
    public function isValidForSend()
    {
        return $this->getTemplateSenderName() && $this->getTemplateSenderEmail() && $this->getTemplateSubject();
    }
    
    /**
     * Return true if template type eq text
     *
     * @return boolean
     */
    public function isPlain()
    {
        return $this->getTemplateType() == self::TYPE_TEXT;
    }
    
    /**
     * Save template
     */
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    public function getProcessedTemplate(array $variables = array())
    {
        $processor = new Mage_Newsletter_Filter_Template();
        $variables['this'] = $this;
        $processor
            ->setIncludeProcessor(array($this, 'getInclude'))
            ->setVariables($variables);
        
        return $processor->filter($this->getTemplateText());
    }
    
    public function getInclude($template, array $variables) {
        $thisClass = __CLASS__;
        $includeTemplate = new $thisClass();
        
        $includeTemplate->loadByCode($template);
        echo $includeTemplate->getText();
        
        return $includeTemplate->getProcessedTemplate($variables);
    }
    
    /**
     * Send mail to subscriber
     *
     * @param   Mage_Newsletter_Model_Subscriber|string   $subscriber   subscriber Model or E-mail
     * @param   array                                     $variables    template variables
     * @param   string|null                               $name         receiver name (if subscriber model not specified)
     * @param   Mage_Newsletter_Model_Queue|null          $queue        queue model, used for problems reporting.
     * @return boolean 
     **/
    public function send($subscriber, array $variables = array(), $name=null, Mage_Newsletter_Model_Queue $queue=null) 
    {
        if(!$this->isValidForSend()) {
            return false;
        }
        
        $email = '';
        if($subscriber instanceof Mage_Newsletter_Model_Subscriber) {
            $email = $subscriber->getSubscriberEmail();
            if (is_null($name) && ($subscriber->hasCustomerFirstname() || $subscriber->hasCustomerLastname()) ) {
                $name = $subscriber->getCustomerFirstname() . ' ' . $subscriber->getCustomerLastname();
            }
        } else {
            $email = (string) $subscriber;
        }
        
        if (is_null($name)) {
            $name = $email;
        }
        
        $mail = new Zend_Mail('utf-8');
        $mail->addTo($email, $name);
        $text = $this->getProcessedTemplate($variables);
        
        if($this->isPlain()) {
            $mail->setBodyText($text);
        } else {
            $mail->setBodyHTML($text);
        }
        
        $mail->setSubject($this->getTemplateSubject());
        $mail->setFrom($this->getTemplateSenderEmail(), $this->getTemplateSenderName());
        try {
            $mail->send();
        }
        catch (Exception $e) {
            if($subscriber instanceof Mage_Newsletter_Model_Subscriber) { 
                $problem = Mage::getModel('newsletter/problem');
                $problem->addData(array(
                    "subscriber_id" => $subscriber->getId(),
                    
                ));
            } else {
                throw $e;
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * Delete template from DB
     */
    public function delete()
    {
        $this->getResource()->delete($this->getId());
        $this->setId(null);
        return $this;
    }
    
}