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
 * Test class for \Magento\Downloadable\Model\Product\Type
 */
class Magento_Downloadable_Model_Product_TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Downloadable\Model\Product\Type
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Downloadable\Model\Product\Type');
    }

    /**
     * @magentoDataFixture Magento/Downloadable/_files/product_with_files.php
     */
    public function testDeleteTypeSpecificData()
    {
        $product = Mage::getModel('\Magento\Catalog\Model\Product');
        $product->load(1);
        Mage::app()->setCurrentStore(\Magento\Core\Model\AppInterface::ADMIN_STORE_ID);
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
        $product = Mage::getModel('\Magento\Catalog\Model\Product');
        $product->load(1);

        $links = $this->_model->getLinks($product);
        $this->assertEmpty($links);
        $samples = $this->_model->getSamples($product);
        $this->assertEmpty($samples->getData());
    }
}
