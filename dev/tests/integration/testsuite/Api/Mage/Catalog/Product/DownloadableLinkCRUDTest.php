<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Catalog_Product_DownloadableLinkCRUDTest extends Magento_Test_TestCase_ApiAbstract
{
    /**
     * Test downloadable link create
     *
     * @magentoApiDataFixture Mage/Catalog/Product/_fixture/LinkCRUD.php
     * @return void
     */
    public function testDownloadableLinkCreate()
    {
        $tagFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/LinkCRUD.xml');
        $items = self::simpleXmlToArray($tagFixture->items);

        $productId = Magento_Test_TestCase_ApiAbstract::getFixture('productData')->getId();

        foreach ($items as $item) {
            foreach ($item as $key => $value) {
                if ($value['type'] == 'file') {
                    $filePath = dirname(__FILE__) . '/_fixture/_data/files/' . $value['file']['filename'];
                    $value['file'] = array(
                        'name' => str_replace('/', '_', $value['file']['filename']),
                        'base64_content' => base64_encode(file_get_contents($filePath)),
                        'type' => $value['type']
                    );
                }
                if ($key == 'link' && $value['sample']['type'] == 'file') {
                    $filePath = dirname(__FILE__) . '/_fixture/_data/files/' . $value['sample']['file']['filename'];
                    $value['sample']['file'] = array(
                        'name' => str_replace('/', '_', $value['sample']['file']['filename']),
                        'base64_content' => base64_encode(file_get_contents($filePath))
                    );
                }

                $resultId = $this->call(
                    'product_downloadable_link.add',
                    array(
                        'productId' => $productId,
                        'resource' => $value,
                        'resourceType' => $key
                    )
                );
                $this->assertGreaterThan(0, $resultId);
            }
        }
    }

    /**
     * Test get downloadable link items
     *
     * @return void
     * @magentoApiDataFixture Mage/Catalog/Product/_fixture/DownloadableWithLinks.php
     */
    public function testDownloadableLinkItems()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Magento_Test_TestCase_ApiAbstract::getFixture('downloadable');
        $productId = $product->getId();

        $result = $this->call('product_downloadable_link.list', array('productId' => $productId));
        /** @var Mage_Downloadable_Model_Product_Type $downloadable */
        $downloadable = $product->getTypeInstance();
        $links = $downloadable->getLinks($product);

        $this->assertEquals(count($links), count($result['links']));
        foreach ($result['links'] as $actualLink) {
            foreach ($links as $expectedLink) {
                if ($actualLink['link_id'] == $expectedLink) {
                    $this->assertEquals($expectedLink->getData('title'), $actualLink['title']);
                    $this->assertEquals($expectedLink->getData('price'), $actualLink['price']);
                }
            }
        }
    }

    /**
     * Remove downloadable link
     *
     * @magentoApiDataFixture Mage/Catalog/Product/_fixture/DownloadableWithLinks.php
     * @return void
     */
    public function testDownloadableLinkRemove()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Magento_Test_TestCase_ApiAbstract::getFixture('downloadable');
        /** @var Mage_Downloadable_Model_Product_Type $downloadable */
        $downloadable = $product->getTypeInstance();
        $links = $downloadable->getLinks($product);
        foreach ($links as $link) {
            $removeResult = $this->call(
                'product_downloadable_link.remove',
                array(
                    'linkId' => $link->getId(),
                    'resourceType' => 'link'
                )
            );
            $this->assertTrue((bool)$removeResult);
        }
    }
}
