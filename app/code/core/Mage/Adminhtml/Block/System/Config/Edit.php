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
    
    protected $_default;
    protected $_config;
    protected $_form;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/system/config/edit.phtml');
        
        $config = Mage::getSingleton('adminhtml/system_config');
        $section = $this->getRequest()->getParam('section');
        
        $this->_default = $config->getNode('admin/configuration/default');
        $this->_config = $config->getNode('admin/configuration/sections/'.$section);
        
        $this->setTitle((string)$this->_config->label);
    }
    
    public function getSaveUrl()
    {
        return Mage::getUrl('*/*/save', array('_current'=>true));
    }
    
    public function initForm()
    {
        $this->setChild('gwstree', 
            $this->getLayout()->createBlock('adminhtml/system_config_gwstree')
                ->initTabs()
        );
        
        $blockName = (string)$this->_config->block;
        if (empty($blockName)) {
            $blockName = self::DEFAULT_SECTION_BLOCK;
        }
        $this->setChild('form', 
            $this->getLayout()->createBlock($blockName)
                ->setSection($this->_config)
                ->setDefaultFrontend($this->_default->descend('field/frontend'))
                ->initForm()
        );
        return $this;
    }
}