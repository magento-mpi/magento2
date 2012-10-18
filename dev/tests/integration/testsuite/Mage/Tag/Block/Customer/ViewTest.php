<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tag
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tag_Block_Customer_ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Tag_Block_Customer_View
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::getObjectManager()->create('Mage_Tag_Block_Customer_View');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testGetMode()
    {
        $structure = Mage::getObjectManager()->create('Magento_Data_Structure');
        $layout = Mage::getModel('Mage_Core_Model_Layout', array('structure' => $structure));
        $layout->addBlock($this->_block, 'test');
        $expected = uniqid();
        $toolbar = Mage::app()->getLayout()->createBlock(
            'Mage_Core_Block_Text',
            '',
            array('data' => array('current_mode' => $expected))
        );
        $this->_block->unsetChild('toolbar');
        $layout->addBlock($toolbar, 'toolbar', 'test');
        $this->assertEquals($expected, $this->_block->getMode());
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_with_image.php
     */
    public function testImage()
    {
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);

        $size = $this->_block->getImageSize();
        $this->assertGreaterThan(1, $size);
        $this->assertContains('/'.$size, $this->_block->getImageUrl($product));
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getImageUrl($product));
    }
}
