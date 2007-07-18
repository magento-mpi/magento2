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
    
    protected function _initChildren() 
    {
    	$this->setChild('backButton', 
    		$this->getLayout()->createBlock('adminhtml/widget_button')
    			->setData(
    				array(
    					'label'   => __('Back'),
    					'onclick' => 'history.back()'
    				)
    			)
    	);
    	
    	$this->setChild('toPlainButton', 
    		$this->getLayout()->createBlock('adminhtml/widget_button')
    			->setData(
    				array(
    					'label'   => __('Convert to Plain Text'),
    					'onclick' => 'templateControl.stripTags();',
    					'id'	  => 'convert_button'
    				)
    			)
    	);
    	
    	$this->setChild('toHtmlButton', 
    		$this->getLayout()->createBlock('adminhtml/widget_button')
    			->setData(
    				array(
    					'label'   => __('Return Html Version'),
    					'onclick' => 'templateControl.unStripTags();',
    					'id'	  => 'convert_button_back',
    					'style'	  => 'display:none'
    				)
    			)
    	);
    	
    	$this->setChild('saveButton', 
    		$this->getLayout()->createBlock('adminhtml/widget_button')
    			->setData(
    				array(
    					'label'   => __('Save Template'),
    					'onclick' => 'templateControl.save();'    					
    				)
    			)
    	);
    	
    	$this->setChild('previewButton', 
    		$this->getLayout()->createBlock('adminhtml/widget_button')
    			->setData(
    				array(
    					'label'   => __('Preview Template'),
    					'onclick' => 'templateControl.preview();'    					
    				)
    			)
    	);
    	
    	$this->setChild('deleteButton', 
    		$this->getLayout()->createBlock('adminhtml/widget_button')
    			->setData(
    				array(
    					'label'   => __('Delete Template'),
    					'onclick' => 'templateControl.deleteTemplate();'    					
    				)
    			)
    	);
    }
    
    public function getBackButtonHtml()
    {
    	return $this->getChildHtml('backButton');
    }
    
    public function getToPlainButtonHtml()
    {
    	return $this->getChildHtml('toPlainButton');
    }
    
    public function getToHtmlButtonHtml()
    {
    	return $this->getChildHtml('toHtmlButton');
    }
    
    public function getSaveButtonHtml()
    {
    	return $this->getChildHtml('saveButton');
    }
    
    public function getPreviewButtonHtml()
    {
    	return $this->getChildHtml('previewButton');
    }
    
    public function getDeleteButtonHtml()
    {
    	return $this->getChildHtml('deleteButton');
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
          return __('Edit Template');
        }
        
        return  __('New Template');
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
        return Mage::getUrl('*/*/save');
    }
    
    /**
     * Return preview action url for form
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return Mage::getUrl('*/*/preview');
    }
    
    public function isTextType()
    {
        return $this->_template->isPlain();
    }
    
    /**
     * Return delete url for customer group
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return Mage::getUrl('*/*/delete', array('id' => $this->_request->getParam('id')));
    }
       
   
}
