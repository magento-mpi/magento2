<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Downloadable/_files/product.php';

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Product');
$product->load(1);

/** @var $linkCollection \Magento\Downloadable\Model\Resource\Link\Collection */
$linkCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Downloadable\Model\Link')->getCollection()
    ->addProductToFilter($product->getId())
    ->addTitleToResult($product->getStoreId())
    ->addPriceToResult($product->getStore()->getWebsiteId());

/** @var $link \Magento\Downloadable\Model\Link */
$link = $linkCollection->getFirstItem();

$requestInfo = new \Magento\Object(array(
    'qty' => 1,
    'links' => array($link->getId())
));

/** @var $cart \Magento\Checkout\Model\Cart */
$cart = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Checkout\Model\Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$objectManager->get('Magento\Core\Model\Registry')->unregister('_singleton/Magento\Checkout\Model\Session');

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$objectManager->removeSharedInstance('Magento\Checkout\Model\Session');
