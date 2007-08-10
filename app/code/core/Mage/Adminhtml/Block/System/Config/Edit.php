<?php
/**
 * Config edit page
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Edit extends Mage_Adminhtml_Block_Widget
{
    const DEFAULT_SECTION_BLOCK = 'adminhtml/system_config_form';
    
    protected $_section;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('system/config/edit.phtml');
        
        $sectionCode = $this->getRequest()->getParam('section');
        
        $this->_section = Mage::getModel('core/config_field')
            ->load($sectionCode, 'path');
        
        $this->setTitle($this->_section->getFrontendLabel());
    }
    
    protected function _initChildren()
    {
        $this->setChild('save_button', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save config'),
                    'onclick'   => 'configForm.submit()',
                    'class' => 'save',
                ))
        );
    }
    
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }
    
    public function getSaveUrl()
    {
        return Mage::getUrl('*/*/save', array('_current'=>true));
    }
    
    public function initForm()
    {
        /*
        $this->setChild('dwstree', 
            $this->getLayout()->createBlock('adminhtml/system_config_dwstree')
                ->initTabs()
        );
        */
        
        $blockName = (string)$this->_section->getFrontendModel();
        if (empty($blockName)) {
            $blockName = self::DEFAULT_SECTION_BLOCK;
        }
        $this->setChild('form', 
            $this->getLayout()->createBlock($blockName)
                ->initForm()
        );
        return $this;
    }
}