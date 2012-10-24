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
 *
 */
class Mage_Downloadable_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Downloadable/_files/product_with_files.php
     */
    public function testDuplicate()
    {
        $this->markTestIncomplete('MAGETWO-4103');
        $currentProduct = new Mage_Catalog_Model_Product;
        $currentProduct->load(1); // fixture for initial product
        $currentLinks = $currentProduct->typeInstance($currentProduct)->getLinks($currentProduct);
        $currentSamples = $currentProduct->getTypeInstance($currentProduct)->getSamples($currentProduct);

        $newProduct = $currentProduct->duplicate();

        $newLinks = $newProduct->getTypeInstance($newProduct)->getLinks($newProduct);
        $newSamples = $newProduct->getTypeInstance($newProduct)->getSamples($newProduct);

        $this->assertEquals($currentLinks, $newLinks,
            'File for links has been lost after duplication');
        $this->assertEquals($currentSamples, $newSamples,
            'File for samples has been lost after duplication');
    }
}
