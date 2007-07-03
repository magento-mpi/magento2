<?php
/**
 * Adminhtml newsletter template edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Template_Edit extends Mage_Adminhtml_Block_Widget
{
    protected $_template;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/newsletter/template/edit.phtml');
        $this->_template = Mage::getModel('newsletter/template');
        if ($templateId = (int) $this->_request->getParam('id')) {
            $this->_template->load($templateId);
        }
    }
    
    /**
     * Set edit flag for block
     * 
     * @param boolean $value
     * @return Mage_Adminhtml_Block_Newsletter_Template_Edit
     */
    public function setEditMode($value=true)
    {
        $this->_editMode = $value;
        return $this;
    }
    
    /**
     * Return edit flag for block
     *
     * @return boolean
     */
    public function getEditMode()
    {
        return $this->_editMode;
    }
    
    /**
     * Return header text for form
     *
     * @return string
     */
    public function getHeaderText() 
    {
        if($this->getEditMode()) {
          return __('edit template');
        }
        
        return  __('new template');
    }
    
       
    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getForm() 
    {
        return $this->getLayout()->createBlock('adminhtml/newsletter_template_edit_form')
            ->renderPrepare($this->_template)
            ->toHtml();
    }
    
    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return Mage::getUrl('adminhtml/*/save');
    }
    
    public function isTextType()
    {
        return $this->_template->getTemplateType() == constant(Mage::getConfig()->getModelClassName('newsletter/template') 
                                                        . '::TYPE_TEXT');
    }
    
    /**
     * Return delete url for customer group
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return Mage::getUrl('adminhtml/*/delete', array('id' => $this->_request->getParam('id')));
    }
    
    /**
     * Prepares block for rendering
     * 
     * @return Mage_Adminhtml_Block_Newsletter_Template_Edit
     */
    protected function _beforeToHtml()
    {   
        
        
        return $this;
    }
    
    
}