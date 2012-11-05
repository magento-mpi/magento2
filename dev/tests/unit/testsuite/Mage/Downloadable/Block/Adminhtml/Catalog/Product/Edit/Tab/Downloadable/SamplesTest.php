<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Downloadable
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_SamplesTest
    extends Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_TestCaseAbstract
{
    protected function setUp()
    {
        parent::setUp();

        $this->_block = $this->getBlock(
            'Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples',
            array(
                'urlBuilder' => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false)
            )
        );
    }

    /**
     * Test that getConfig method retrieve Varien_Object object
     */
    public function testGetConfig()
    {
        // we have to set strict error reporting mode and enable mage developer mode to convert notice to exception
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', 1);
        Mage::setIsDeveloperMode(true);

        $this->assertInstanceOf('Varien_Object', $this->_block->getConfig());
    }
}
