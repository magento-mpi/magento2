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

require __DIR__ . '/../../../Magento/Sales/_files/order.php';

// refresh report statistics
/** @var \Magento\Sales\Model\Resource\Report\Order $reportResource */
$reportResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Sales\Model\Resource\Report\Order'
);
try {
    $reportResource->aggregate();
} catch (\Exception $e) {
    throw $e;
}
