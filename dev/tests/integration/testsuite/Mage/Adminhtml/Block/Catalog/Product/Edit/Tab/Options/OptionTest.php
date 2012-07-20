<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_OptionTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option */
    protected $_block = null;

    public function setUp()
    {
        $this->_block = Mage::app()->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option');
    }

    public function testGetOptionValuesCaching()
    {
        $productWithOptions = new Mage_Catalog_Model_Product();
        $productWithOptions->setTypeId('simple')
            ->setId(1)
            ->setAttributeSetId(4)
            ->setWebsiteIds(array(1))
            ->setName('Simple Product With Custom Options')
            ->setSku('simple')
            ->setPrice(10)

            ->setMetaTitle('meta title')
            ->setMetaKeyword('meta keyword')
            ->setMetaDescription('meta description')

            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        $productWithoutOptions = clone $productWithOptions;

        $option = new Mage_Catalog_Model_Product_Option(array('id' => 1, 'title' => 'some_title'));
        $productWithOptions->addOption($option);

        $this->_block->setProduct($productWithOptions);
        $this->assertNotEmpty($this->_block->getOptionValues());

        $this->_block->setProduct($productWithoutOptions);
        $this->assertNotEmpty($this->_block->getOptionValues());

        $this->_block->setIgnoreCaching(true);
        $this->assertEmpty($this->_block->getOptionValues());
    }
}
