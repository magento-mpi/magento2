<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Magento_Catalog_Model_Product_ImageTest
 * @magentoAppArea frontend
 */
class Magento_Catalog_Model_Product_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return Magento_Catalog_Model_Product_Image
     */
    public function testSetBaseFilePlaceholder()
    {
        /** @var $model Magento_Catalog_Model_Product_Image */
        $model = Mage::getModel('Magento_Catalog_Model_Product_Image');
        $model->setDestinationSubdir('image')->setBaseFile('');
        $this->assertEmpty($model->getBaseFile());
        return $model;
    }

    /**
     * @param Magento_Catalog_Model_Product_Image $model
     * @depends testSetBaseFilePlaceholder
     */
    public function testSaveFilePlaceholder($model)
    {
        $processor = $this->getMock('Magento_Image', array('save'), array(), '', false);
        $processor->expects($this->exactly(0))->method('save');
        $model->setImageProcessor($processor)->saveFile();
    }

    /**
     * @param Magento_Catalog_Model_Product_Image $model
     * @depends testSetBaseFilePlaceholder
     */
    public function testGetUrlPlaceholder($model)
    {
        $this->assertStringMatchesFormat(
            'http://localhost/pub/static/frontend/%s/Magento_Catalog/images/product/placeholder/image.jpg',
            $model->getUrl()
        );
    }
}
