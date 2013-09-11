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

require __DIR__ . '/../../Checkout/_files/simple_product.php';
/** @var $product \Magento\Catalog\Model\Product */
$product = Mage::getModel('\Magento\Catalog\Model\Product');
$product->load(1);

/** @var $product \Magento\Catalog\Model\Product */
$product->setCanSaveCustomOptions(true)
    ->setProductOptions(
        array(
            array(
                'id'        => 1,
                'option_id' => 0,
                'previous_group' => 'text',
                'title'     => 'Test Field',
                'type'      => 'field',
                'is_require'=> 1,
                'sort_order'=> 0,
                'price'     => 1,
                'price_type'=> 'fixed',
                'sku'       => '1-text',
                'max_characters' => 100
            )
        )
    )
    ->setHasOptions(true)
    ->save();

/** @var $product \Magento\Catalog\Model\Product */
$product = Mage::getModel('\Magento\Catalog\Model\Product');
$product->load(1);
$optionId = key($product->getOptions());

$requestInfo = new \Magento\Object(array(
    'qty' => 1,
    'options' => array(
        $optionId => 'test'
    )
));

require __DIR__ . '/../../Checkout/_files/cart.php';
