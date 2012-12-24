<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$fixture = simplexml_load_file(__DIR__ . '/_data/xml/LinkCRUD.xml');

//Create new downloadable product
$productData = Magento_Test_Webservice::simpleXmlToArray($fixture->product);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);
$linkData = Magento_Test_Webservice::simpleXmlToArray($fixture->items->small->link);
unset($linkData['sample']['file']);
unset($linkData['file']);


$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setData($productData)
    ->setStoreId(0)
    ->setDownloadableData(array('link' => array($linkData)))
    ->save();
Magento_Test_Webservice::setFixture('downloadable', $product);
