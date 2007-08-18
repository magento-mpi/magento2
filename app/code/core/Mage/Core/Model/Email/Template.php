<?php
/**
 * Template model
 *
 * Example:
 * 
 * // Loading of template
 * $emailTemplate  = Mage::getModel('core/email_template')
 *    ->load(Mage::getStoreConfig('path_to_email_template_id_config'));
 * $variables = array(
 *    'someObject' => Mage::getSingleton('some_model')
 *    'someString' => 'Some string value'
 * );
 * $emailTemplate->send('some@domain.com', 'Name Of User', $variables);
 * 
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Core_Model_Email_Template extends Varien_Object
{
    /**
     * Types of template
     */
    const TYPE_TEXT = 1;
    const TYPE_HTML = 2;

    protected $_templateFilter;
    protected $_preprocessFlag = false;
    
    /** 
     * Return resource of template model.
     *
     * @return Mage_Newsletter_Model_Mysql4_Template
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('core/email_template');
    }
    
    public function getTemplateFilter()
    {
    	if (empty($this->_templateFilter)) {
    		$this->_templateFilter = Mage::getModel('core/email_template_filter');
    	}
    	return $this->_templateFilter;
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
        return $this->getSenderName() && $this->getSenderEmail() && $this->getTemplateSubject();
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
        $processor = $this->getTemplateFilter();
        
        if(!$this->_preprocessFlag) {
        	$variables['this'] = $this;
        }
        
        $processor
            ->setIncludeProcessor(array($this, 'getInclude'))
            ->setVariables($variables);
       
        
        return $processor->filter($this->getTemplateText());
    }
    
    
    
    public function getInclude($template, array $variables)
    {
        $thisClass = __CLASS__;
        $includeTemplate = new $thisClass();
        
        $includeTemplate->loadByCode($template);
        
        return $includeTemplate->getProcessedTemplate($variables);
    }
    
    /**
     * Send mail to recipient
     *
     * @param   string      $email		  E-mail
     * @param   string|null $name         receiver name
     * @param   array       $variables    template variables
     * @return boolean 
     **/
    public function send($email, $name=null, array $variables = array())
    {
        if(!$this->isValidForSend()) {
            return false;
        }
        
        if (is_null($name)) {
            $name = substr($email, 0, strpos($email, '@'));
        }
                
        $variables['email'] = $email;
        $variables['name'] = $name;
        
        $mail = new Zend_Mail('utf-8');
        $mail->addTo($email, $name);
        $text = $this->getProcessedTemplate($variables, true);
        
        if($this->isPlain()) {
            $mail->setBodyText($text);
        } else {
            $mail->setBodyHTML($text);
        }
                    
        $mail->setSubject($this->getProcessedTemplateSubject($variables));
        $mail->setFrom($this->getSenderEmail(), $this->getSenderName());
        try {
            $mail->send();
        }
        catch (Exception $e) {
            return false;
        }
        
        return true;
    }
    
    public function sendTransactional($templateId, $sender, $email, $name, $vars=array(), $storeId=null)
    {
    	if (is_null($storeId)) {
    		$storeId = Mage::getSingleton('core/store')->getId();
    	}
    	/*$templateId = Mage::getStoreConfig("trans_email/trans_{$transCode}/template", $storeId);
    	$identity = Mage::getStoreConfig("trans_email/trans_{$transCode}/identity", $storeId);*/
    	
    	$this->load($templateId);
    	if (!$this->getId()) {
    		throw Mage::exception('Mage_Core', 'Invalid transactional email code');
    	}
    	$this->setSenderName(Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId));
    	$this->setSenderEmail(Mage::getStoreConfig('trans_email/ident_'.$sender.'/email', $storeId));
    	$this->send($email, $name, $vars);
    	return $this;
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
    
    
    public function getProcessedTemplateSubject(array $variables) 
    {
    	$processor = $this->getTemplateFilter();
        
        if(!$this->_preprocessFlag) {
        	$variables['this'] = $this;
        }
        
        $processor->setVariables($variables);
        
        return $processor->filter($this->getTemplateSubject());
    }
}