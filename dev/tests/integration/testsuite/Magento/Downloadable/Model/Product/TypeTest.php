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
 * Test class for Magento_Downloadable_Model_Product_Type
 */
class Magento_Downloadable_Model_Product_TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Downloadable_Model_Product_Type
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Downloadable_Model_Product_Type');
    }

    /**
     * @magentoDataFixture Magento/Downloadable/_files/product_with_files.php
     */
    public function testDeleteTypeSpecificData()
    {
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->load(1);
        Mage::app()->setCurrentStore(Magento_Core_Model_AppInterface::ADMIN_STORE_ID);
        $product->setOrigData();
        $downloadableData = array();

        $links = $this->_model->getLinks($product);
        $this->assertNotEmpty($links);
        $samples = $this->_model->getSamples($product);
        $this->assertNotEmpty($samples->getData());
        foreach ($links as $link) {
            $downloadableData['link'][] = $link->getData();
        }
        foreach ($samples as $sample) {
            $downloadableData['sample'][] = $sample->getData();
        }

        $product->setDownloadableData($downloadableData);
        $this->_model->deleteTypeSpecificData($product);
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->load(1);

        $links = $this->_model->getLinks($product);
        $this->assertEmpty($links);
        $samples = $this->_model->getSamples($product);
        $this->assertEmpty($samples->getData());
    }
}
