<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

Mage::app()->loadAreaPart('adminhtml', \Magento\Core\Model\App\Area::PART_CONFIG);

require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';
require __DIR__ . '/../../../Magento/Catalog/_files/product_simple_duplicated.php';
require __DIR__ . '/../../../Magento/Catalog/_files/product_virtual.php';

// imitate product views
/** @var \Magento\Reports\Model\Event\Observer $reportObserver */
$reportObserver = Mage::getModel('Magento\Reports\Model\Event\Observer');
foreach (array(1, 2, 1, 21, 1, 21) as $productId) {
    $reportObserver->catalogProductView(new \Magento\Event\Observer(array(
        'event' => new \Magento\Object(array(
            'product' => new \Magento\Object(array('id' => $productId))
        ))
    )));
}

// refresh report statistics
/** @var \Magento\Reports\Model\Resource\Report\Product\Viewed $reportResource */
$reportResource = Mage::getResourceModel('Magento\Reports\Model\Resource\Report\Product\Viewed');
$reportResource->beginTransaction(); // prevent table truncation by incrementing the transaction nesting level counter
try {
    $reportResource->aggregate();
    $reportResource->commit();
} catch (Exception $e) {
    $reportResource->rollBack();
    throw $e;
}
