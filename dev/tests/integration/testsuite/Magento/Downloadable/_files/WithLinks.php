<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

$fixture = simplexml_load_file(__DIR__ . '/_data/xml/LinkCRUD.xml');

//Create new downloadable product
$productData = Magento_TestFramework_Helper_Api::simpleXmlToArray($fixture->product);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);
$linksData = array(
    array(
        'title' => 'Test Link 1',
        'price' => '1',
        'is_unlimited' => '1',
        'number_of_downloads' => '0',
        'is_shareable' => '0',
        'sample' => array(
            'type' => 'url',
            'url' => 'http://www.magentocommerce.com/img/logo.gif',
        ),
        'type' => 'url',
        'link_url' => 'http://www.magentocommerce.com/img/logo.gif',
    ),
    array(
        'title' => 'Test Link 2',
        'price' => '2',
        'is_unlimited' => '0',
        'number_of_downloads' => '10',
        'is_shareable' => '1',
        'sample' =>
        array(
            'type' => 'url',
            'url' => 'http://www.magentocommerce.com/img/logo.gif',
        ),
        'type' => 'url',
        'link_url' => 'http://www.magentocommerce.com/img/logo.gif',
    ),
);

$product = Mage::getModel('\Magento\Catalog\Model\Product');
$product->setData($productData)
    ->setStoreId(0)
    ->setDownloadableData(array('link' => $linksData))
    ->save();
Mage::register('downloadable', $product);
