<?php
/**
 * Template db resource
 * 
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Newsletter_Model_Mysql4_Template {

    /**
     * Templates table name
     * @var string
     */
    protected $_templateTable;
    
    /** 
     * Queue table name 
     * @var string
     */
    protected $_queueTable;
    
    /**
     * DB write connection
     */
    protected $_write;
    
    /**
     * DB read connection
     */
    protected $_read;
    
    /**
     * Constructor
     * 
     * Initializes resource
     */
    public function __construct() 
    {
        $this->_templateTable = Mage::getSingleton('core/resource')->getTableName('newsletter/template');
        $this->_queueTable = Mage::getSingleton('core/resource')->getTableName('newsletter/queue');
        $this->_read = Mage::getSingleton('core/resource')->getConnection('newsletter_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('newsletter_write');
    }
    
    /**
     * Load template from DB
     *
     * @param  int $templateId 
     * @return array
     */
    public function load($templateId)
    {
        $select = $this->_read->select()
            ->from($this->_templateTable)
            ->where('template_id=?', $templateId);
        
        $result = $this->_read->fetchRow($select);
        
        if (!$result) {
            return array();
        }
        
        return $result;
    }
    
    /**
     * Load by template code from DB
     *
     * @param  int $templateId 
     * @return array
     */
    public function loadByCode($templateCode)
    {
        $select = $this->_read->select()
            ->from($this->_templateTable)
            ->where('template_code=?', $templateCode)
            ->where('template_actual=?','true');
        
        $result = $this->_read->fetchRow($select);
        
        if (!$result) {
            return array();
        }
        
        return $result;
    }
    
    /**
     * Check usage of template in queue
     *
     * @param  Mage_Newsletter_Model_Template $template
     * @return boolean
     */
    public function checkUsageInQueue(Mage_Newsletter_Model_Template $template)
    {
        if($template->getTemplateActual()!=='false') {
            $select = $this->_read->select()
                ->from($this->_queueTable, new Zend_Db_Expr('COUNT(queue_id)'))
                ->where('template_id=?',$template->getId());
            
            $countOfQueue = $this->_read->fetchOne($select);
            
            return $countOfQueue > 0;
        } else {       
            return true;
        }
    }
    
    /**
     * Check usage of template code in other templates
     *
     * @param   Mage_Newsletter_Model_Template $template
     * @return  boolean
     */
    public function checkCodeUsage(Mage_Newsletter_Model_Template $template)
    {
        if($template->getTemplateActual()!=='false') {
            $select = $this->_read->select()
                ->from($this->_templateTable, new Zend_Db_Expr('COUNT(template_id)'))
                ->where('template_id!=?',$template->getId())
                ->where('template_code=?',$template->getTemplateCode())
                ->where('template_actual=?','true');
            
            $countOfCodes = $this->_read->fetchOne($select);
            
            return $countOfCodes > 0;
        } else {
            return false;
        }
    }
    
    /**
     * Save template to DB
     *
     * @param   Mage_Newsletter_Model_Template $template
     */
    public function save(Mage_Newsletter_Model_Template $template) 
    {
        $this->_write->beginTransaction();
        try {
            $data = $this->_prepareSave($template);
            if($template->getId() && ($template->getTemplateActual()=='false' || !$this->checkUsageInQueue($template))) {
                $this->_write->update($this->_templateTable, $data, 
                                      $this->_write->quoteInto('template_id=?',$template->getId())); 
            } else if ($template->getId()) {
                // Duplicate entry if template used in queue
                $updata = array();
                $updata['template_actual'] = 'false';
                $this->_write->update($this->_templateTable, $updata, 
                                      $this->_write->quoteInto('template_id=?',$template->getId()));
                
                $this->_write->insert($this->_templateTable, $data);
                $template->setId($this->_write->lastInsertId($this->_templateTable));
            } else {
                $this->_write->insert($this->_templateTable, $data);
                $template->setId($this->_write->lastInsertId($this->_templateTable));
            }
            
            $this->_write->commit();
        }
        catch (Exception $e) {
            $this->_write->rollBack();
            Mage::throwException('cannot save newsletter template');
        }
    }
    
    /**
     * Prepares template for saving, validates input data
     *
     * @param   Mage_Newsletter_Model_Template $template
     * @return  array
     */
    protected function _prepareSave(Mage_Newsletter_Model_Template $template) 
    {
        $data = array();
        $data['template_code'] = $template->getTemplateCode();
        $data['template_text'] = $template->getTemplateText();
        $data['template_type'] = (int) $template->getTemplateType();
        $data['template_subject'] = $template->getTemplateSubject();
        $data['template_sender_name'] = $template->getTemplateSenderName();
        $data['template_sender_email'] = $template->getTemplateSenderEmail();
        $data['template_actual'] = $template->getTemplateActual() === 'false' ? 'false' : 'true';
        
        if($this->checkCodeUsage($template)) {
            Mage::throwException('duplicate of template code');
        }
        $templateCodeValidator = new Zend_Validate_Regex('/^[a-z][a-z0-9\\-_]*$/i');
        $templateCodeValidator->setMessage('invalid template code', Zend_Validate_Regex::NOT_MATCH);
        $validators = array( 
            'template_code' => array(
                $templateCodeValidator                                
            ),
            'template_type' => 'Alnum',
            'template_sender_email' => array(
                new Zend_Validate_EmailAddress(),
                Zend_Filter_Input::ALLOW_EMPTY => true
            )
        );
        
        $validateInput = new Zend_Filter_Input(array(), $validators, $data);
        if(!$validateInput->isValid()) {
            $errorString = '';
            foreach($validateInput->getMessages() as $message) {
                $errorString.= $message . "\n";
            }            
            Mage::throwException($errorString);
        }
        
        return $data;
    }
    
    /**
     * Delete template record in DB.
     *
     * @param   int $templateId
     */
    public function delete($templateId)
    {
        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->_templateTable, $this->_write->quoteInto('template_id=?', $templateId));
            $this->_write->commit();
        }
        catch(Exception $e) {
            $this->_write->rollBack();
            Mage::throwException('cannot delete template');
        }
    }
}