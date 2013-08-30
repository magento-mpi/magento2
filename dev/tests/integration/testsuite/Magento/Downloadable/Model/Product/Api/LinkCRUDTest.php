<?php
/**
 * Downloadable product links API model test.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_Downloadable_Model_Product_Api_LinkCRUDTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test downloadable link create
     *
     * @magentoDataFixture Magento/Downloadable/_files/LinkCRUD.php
     */
    public function testDownloadableLinkCreate()
    {
        $tagFixture = simplexml_load_file(dirname(__FILE__) . '/../../../_files/_data/xml/LinkCRUD.xml');
        $items = Magento_TestFramework_Helper_Api::simpleXmlToArray($tagFixture->items);

        $productId = Mage::registry('productData')->getId();

        foreach ($items as $item) {
            foreach ($item as $key => $value) {
                if ($value['type'] == 'file') {
                    $filePath = dirname(__FILE__)
                              . '/../../../../../Magento/Catalog/Model/Product/Api/_files/_data/files/'
                              . $value['file']['filename'];
                    $value['file'] = array(
                        'name' => str_replace('/', '_', $value['file']['filename']),
                        'base64_content' => base64_encode(file_get_contents($filePath)),
                        'type' => $value['type']
                    );
                }
                if ($key == 'link' && $value['sample']['type'] == 'file') {
                    $filePath = dirname(__FILE__)
                              . '/../../../../../Magento/Catalog/Model/Product/Api/_files/_data/files/'
                              . $value['sample']['file']['filename'];
                    $value['sample']['file'] = array(
                        'name' => str_replace('/', '_', $value['sample']['file']['filename']),
                        'base64_content' => base64_encode(file_get_contents($filePath))
                    );
                }

                $resultId = Magento_TestFramework_Helper_Api::call(
                    $this,
                    'catalogProductDownloadableLinkAdd',
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
     * @magentoDataFixture Magento/Downloadable/_files/WithLinks.php
     */
    public function testDownloadableLinkItems()
    {
        /** @var Magento_Catalog_Model_Product $product */
        $product = Mage::registry('downloadable');
        $productId = $product->getId();

        $result = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductDownloadableLinkList',
            array('productId' => $productId)
        );
        /** @var Magento_Downloadable_Model_Product_Type $downloadable */
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
     * @magentoDataFixture Magento/Downloadable/_files/WithLinks.php
     */
    public function testDownloadableLinkRemove()
    {
        /** @var Magento_Catalog_Model_Product $product */
        $product = Mage::registry('downloadable');
        /** @var Magento_Downloadable_Model_Product_Type $downloadable */
        $downloadable = $product->getTypeInstance();
        $links = $downloadable->getLinks($product);
        foreach ($links as $link) {
            $removeResult = Magento_TestFramework_Helper_Api::call(
                $this,
                'catalogProductDownloadableLinkRemove',
                array(
                    'linkId' => $link->getId(),
                    'resourceType' => 'link'
                )
            );
            $this->assertTrue((bool)$removeResult);
        }
    }
}
