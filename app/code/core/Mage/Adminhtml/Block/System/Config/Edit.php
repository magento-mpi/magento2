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
    
    protected $_websiteCode;
    protected $_storeCode;
    protected $_sectionCode;
    
    protected $_config;
    protected $_activeSection;
    protected $_activeSectionCode;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/system/config/edit.phtml');

        $this->_websiteCode = $this->getRequest()->getParam('website');
        $this->_storeCode   = $this->getRequest()->getParam('store');
        $this->_sectionCode = $this->getRequest()->getParam('section');
        
        $config = Mage::getSingleton('adminhtml/system_config');
        if (!$this->_websiteCode) {
            $this->_config = (array) $config->getNode('configuration/global/sections');
        }
        elseif (!$this->_storeCode){
            $this->_config = (array) $config->getNode('configuration/website/sections');
        }
        else {
            $this->_config = (array) $config->getNode('configuration/store/sections');
        }
        
        if (isset($this->_config[$this->_sectionCode])) {
            $this->_activeSection = $this->_config[$this->_sectionCode];
        }
        else {
            $keys = array_keys($this->_config);
            $this->_activeSection = $this->_config[$keys[0]];
            $this->_sectionCode = $keys[0];
        }
    }
    
    public function getSaveUrl()
    {
        return Mage::getUrl('adminhtml/*/save', array('_current'=>true));
    }
    
    public function getTitle()
    {
        //return __('edit config');
        return '';
    }
    
    public function getForm()
    {
        $blockName = (string)$this->_activeSection->block;
        if (empty($blockName)) {
            $blockName = self::DEFAULT_SECTION_BLOCK;
        }
        return $this->getLayout()->createBlock($blockName)
            ->setSection($this->_activeSection)
            ->toHtml();
    }
    
    public function getSections()
    {
        $sections = array();
        foreach ($this->_config as $code => $section) {
            $sections[] = new Varien_Object(array(
                'label' => __($code),
                'url'   => Mage::getUrl('adminhtml/*/*', array('_current'=>true, 'section'=>$code)),
                'class' => ($code == $this->_sectionCode) ? 'active' : ''
            ));
        }
        return $sections;
    }
 
    protected function _beforeToHtml()
    {
        return $this;
    }
    
    public function bindBreadcrumbs($breadcrumbs)
    {
        $breadcrumbs->addLink(__($this->_sectionCode), '');
        return $this;
    }
}