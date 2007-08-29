<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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