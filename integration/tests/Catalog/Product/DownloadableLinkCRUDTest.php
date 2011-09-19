<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @magentoDataFixture Catalog/Product/_fixtures/LinkCRUD.php
 */
class Catalog_Product_DownloadableLinkCRUDTest extends Magento_Test_Webservice
{
    public function testLogin()
    {
        $this->assertNotEmpty($this->getWebService()->login('api', 'apiapi'));
    }

    public function testDownloadableLinkCreate()
    {
        $tagFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/LinkCRUD.xml');
        $items = self::simpleXmlToArray($tagFixture->items);

        $product_id = Magento_Test_Webservice::getFixture('productData')->getId();

        foreach ($items as $item) {
            foreach ($item as $key => $value) {
                if ($value['type'] == 'file') {
                    $filePath = dirname(__FILE__) . '/_fixtures/files/' . $value['file']['filename'];
                    $value['file'] = array('name' => str_replace('/', '_', $value['file']['filename']),
                        'base64_content' => base64_encode(file_get_contents($filePath)), 'type' => $value['type']);
                }
                if ($key == 'link' && $value['sample']['type'] == 'file') {
                    $filePath = dirname(__FILE__) . '/_fixtures/files/' . $value['sample']['file']['filename'];
                    $value['sample']['file'] = array('name' => str_replace('/', '_',$value['sample']['file']['filename']),
                        'base64_content' => base64_encode(file_get_contents($filePath)));
                }

                $resultId = $this->call('product_downloadable_link.add',
                    array($product_id, $value, $key)
                );
                $this->assertGreaterThan(0, $resultId);
            }
        }

    }

}
