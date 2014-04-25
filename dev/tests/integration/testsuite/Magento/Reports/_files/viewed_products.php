<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\AreaList')
    ->getArea('adminhtml')
    ->load(\Magento\Framework\App\Area::PART_CONFIG);

require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';
require __DIR__ . '/../../../Magento/Catalog/_files/product_simple_duplicated.php';
require __DIR__ . '/../../../Magento/Catalog/_files/product_virtual.php';

// imitate product views
/** @var \Magento\Reports\Model\Event\Observer $reportObserver */
$reportObserver = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Reports\Model\Event\Observer'
);
foreach (array(1, 2, 1, 21, 1, 21) as $productId) {
    $reportObserver->catalogProductView(
        new \Magento\Framework\Event\Observer(
            array(
                'event' => new \Magento\Framework\Object(
                        array(
                            'product' => new \Magento\Framework\Object(array('id' => $productId))
                        )
                    )
            )
        )
    );
}

// refresh report statistics
/** @var \Magento\Reports\Model\Resource\Report\Product\Viewed $reportResource */
$reportResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Reports\Model\Resource\Report\Product\Viewed'
);
$reportResource->beginTransaction();
// prevent table truncation by incrementing the transaction nesting level counter
try {
    $reportResource->aggregate();
    $reportResource->commit();
} catch (\Exception $e) {
    $reportResource->rollBack();
    throw $e;
}
