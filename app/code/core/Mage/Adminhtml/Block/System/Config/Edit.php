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
class Mage_Adminhtml_Block_System_Config_Edit extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_websiteCode;
    protected $_storeCode;
    protected $_sectionCode;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/system/config/edit.phtml');

        $this->_websiteCode = $this->_request->getParam('website');
        $this->_storeCode   = $this->_request->getParam('store');
        $this->_sectionCode = $this->_request->getParam('section');
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
    
    public function getSections()
    {
        $sections = array(
        );
        return $sections;
    }
 
    protected function _beforeToHtml()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('config_fieldset', array('legend'=>__('configuration form')));
        $fieldset->addField('test', 'text', 
            array(
                'name'  => 'test',
                'label' => __('test field'),
                'title' => __('test field title'),
                'class' => 'required-entry',
            ),
            'password'
        );
        
        $this->setForm($form);
        return $this;
    }
}