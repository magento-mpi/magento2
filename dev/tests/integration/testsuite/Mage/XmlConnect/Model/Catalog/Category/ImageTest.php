<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_XmlConnect
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Xmlconnect
 */
class Mage_XmlConnect_Model_Catalog_Category_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function testSetBaseFilePlaceholder()
    {
        $model = new Mage_XmlConnect_Model_Catalog_Category_Image;
        $model->setDestinationSubdir('image')->setBaseFile('');
        $this->assertEmpty($model->getBaseFile());
        return $model;
    }

    /**
     * @param Mage_Catalog_Model_Product_Image $model
     * @depends testSetBaseFilePlaceholder
     */
    public function testGetUrlPlaceholder($model)
    {
        $this->assertStringMatchesFormat(
            'http://localhost/media/skin/frontend/%s/Mage_XmlConnect/images/catalog/category/placeholder/image.jpg',
            $model->getUrl()
        );
    }
}
