<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Downloadable_Model_Product_Type
 */
class Mage_Downloadable_Model_Product_TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Downloadable_Model_Product_Type
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Downloadable_Model_Product_Type();
    }

    /**
     * @magentoDataFixture Mage/Downloadable/_files/product_with_files.php
     */
    public function testDeleteTypeSpecificData()
    {
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);
        Mage::app()->getStore()->setId(0);
        $product->setOrigData();
        $downloadableData = array();

        $links = $this->_model->getLinks($product);
        $this->assertNotEmpty($links);

        foreach ($links as $link) {
            $downloadableData['link'][] = $link->getData();
        }
        $product->setDownloadableData($downloadableData);
        $this->_model->deleteTypeSpecificData($product);
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);

        $links = $this->_model->getLinks($product);
        $this->assertEmpty($links);
    }
}
