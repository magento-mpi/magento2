<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of block injection classes
     *
     * @var array
     */
    protected $_blockInjections = array(
        'Mage_Core_Controller_Request_Http',
        'Mage_Core_Model_Layout',
        'Mage_Core_Model_Event_Manager',
        'Mage_Backend_Model_Url',
        'Mage_Core_Model_Translate',
        'Mage_Core_Model_Cache',
        'Mage_Core_Model_Design_Package',
        'Mage_Core_Model_Session',
        'Mage_Core_Model_Store_Config',
        'Mage_Core_Controller_Varien_Front',
        'Mage_Core_Model_Factory_Helper'
    );

    /** @var Mage_Core_Model_Layout */
    protected $_layout = null;

    /** @var Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Form */
    protected $_block = null;

    protected function setUp()
    {
        $this->_layout = Mage::getModel('Mage_Core_Model_Layout');
        $this->_block = $this->_layout->createBlock('Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Form');
    }

    protected function tearDown()
    {
        $this->_block = null;
        $this->_layout = null;
    }

    public function testGetGridJsObject()
    {
        $parentName = 'parent';
        $mockClass = $this->getMockClass('Mage_Catalog_Block_Product_Abstract', array('_prepareLayout'),
            $this->_prepareConstructorArguments()
        );
        $this->_layout->createBlock($mockClass, $parentName);
        $this->_layout->setChild($parentName, $this->_block->getNameInLayout(), '');

        $pageGrid = $this->_layout->addBlock(
            'Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Form_Grid',
            'cms_page_grid',
            $parentName
        );
        $this->assertEquals($pageGrid->getJsObjectName(), $this->_block->getGridJsObject());
    }
    /**
     * List of block constructor arguments
     *
     * @return array
     */
    protected function _prepareConstructorArguments()
    {
        $arguments = array();
        foreach ($this->_blockInjections as $injectionClass) {
            $arguments[] = Mage::getModel($injectionClass);
        }
        return $arguments;
    }
}
