<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Core_Model_Layout */
    protected $_layout = null;

    /** @var Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Edit_Form */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
        $this->_block = $this->_layout->createBlock('Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Edit_Form');
    }

    public function testGetGridJsObject()
    {
        $parentName = 'parent';
        $mockClass = $this->getMockClass('Magento_Catalog_Block_Product_Abstract', array('_prepareLayout'),
            array(Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Block_Template_Context'))
        );
        $this->_layout->createBlock($mockClass, $parentName);
        $this->_layout->setChild($parentName, $this->_block->getNameInLayout(), '');

        $pageGrid = $this->_layout->addBlock(
            'Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Edit_Form_Grid',
            'cms_page_grid',
            $parentName
        );
        $this->assertEquals($pageGrid->getJsObjectName(), $this->_block->getGridJsObject());
    }
}
