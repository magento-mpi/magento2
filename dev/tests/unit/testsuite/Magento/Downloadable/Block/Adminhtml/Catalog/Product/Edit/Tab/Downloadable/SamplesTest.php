<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_SamplesTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links
     */
    protected $_block;

    protected function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $objectManagerHelper->getObject(
            'Magento_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples',
            array(
                'urlBuilder' => $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false)
            )
        );
    }

    /**
     * Test that getConfig method retrieve Magento_Object object
     */
    public function testGetConfig()
    {
        $this->assertInstanceOf('Magento_Object', $this->_block->getConfig());
    }
}
