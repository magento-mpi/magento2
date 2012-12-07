<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture testsuite/Api/Catalog/Product/_fixture/LinkCRUD.php
 */
class Api_Catalog_Product_DownloadableLinkCRUDTest extends Magento_Test_Webservice
{
    protected static $links = array();

    /**
     * Test downloadable link create
     *
     * @return void
     */
    public function testDownloadableLinkCreate()
    {
        $tagFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/LinkCRUD.xml');
        $items = self::simpleXmlToArray($tagFixture->items);

        $product_id = Magento_Test_Webservice::getFixture('productData')->getId();

        foreach ($items as $item) {
            foreach ($item as $key => $value) {
                if ($value['type'] == 'file') {
                    $filePath = dirname(__FILE__) . '/_fixture/_data/files/' . $value['file']['filename'];
                    $value['file'] = array('name' => str_replace('/', '_', $value['file']['filename']),
                        'base64_content' => base64_encode(file_get_contents($filePath)), 'type' => $value['type']);
                }
                if ($key == 'link' && $value['sample']['type'] == 'file') {
                    $filePath = dirname(__FILE__) . '/_fixture/_data/files/' . $value['sample']['file']['filename'];
                    $value['sample']['file'] = array(
                                        'name' => str_replace('/', '_',$value['sample']['file']['filename']
                                    ),
                        'base64_content' => base64_encode(file_get_contents($filePath)));
                }

                $resultId = $this->call('product_downloadable_link.add', array(
                    'productId' => $product_id,
                    'resource' => $value,
                    'resourceType' => $key));
                $this->assertGreaterThan(0, $resultId);
            }
        }
    }

    /**
     * Test get downloadable link items
     *
     * @return void
     */
    public function testDownloadableLinkItems()
    {
        $tagFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/LinkCRUD.xml');
        $fixtureItems = self::simpleXmlToArray($tagFixture->items);

        $product_id = Magento_Test_Webservice::getFixture('productData')->getId();

        self::$links = array();
        $items = $this->call('product_downloadable_link.list', array('productId' => $product_id));
        foreach ($items as $type => $item) {
            switch($type) {
                case "links":
                    $type = 'link';
                    break;
                case "samples":
                    $type = 'sample';
                    break;
                default:
                    $this->fail('Unknown link type: \''.$type.'\'');
                    break;
            }
            foreach ($item as $itemEntity) {
                if (isset($itemEntity['link_id'])) {
                    self::$links[$type][] = $itemEntity['link_id'];
                }
                if (isset($itemEntity['sample_id'])) {
                    self::$links[$type][] = $itemEntity['sample_id'];
                }
            }
        }
        $this->assertEquals(count($fixtureItems), count(self::$links));
        $this->assertNotEmpty(self::$links['link']);
    }

    /**
     * Remove downloadable link
     *
     * @return void
     */
    public function testDownloadableLinkRemove()
    {
        foreach (self::$links as $type => $item) {
            foreach ($item as $link_id) {
                $removeResult = $this->call('product_downloadable_link.remove', array(
                    'linkId' => $link_id, 'resourceType' => $type));
                $this->assertTrue((bool)$removeResult);
            }
        }
    }
}
