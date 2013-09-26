<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App')
    ->loadAreaPart('adminhtml', Magento_Core_Model_App_Area::PART_CONFIG);

require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';
require __DIR__ . '/../../../Magento/Catalog/_files/product_simple_duplicated.php';
require __DIR__ . '/../../../Magento/Catalog/_files/product_virtual.php';

// imitate product views
/** @var Magento_Reports_Model_Event_Observer $reportObserver */
$reportObserver = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Reports_Model_Event_Observer');
foreach (array(1, 2, 1, 21, 1, 21) as $productId) {
    $reportObserver->catalogProductView(new Magento_Event_Observer(array(
        'event' => new Magento_Object(array(
            'product' => new Magento_Object(array('id' => $productId))
        ))
    )));
}

// refresh report statistics
/** @var Magento_Reports_Model_Resource_Report_Product_Viewed $reportResource */
$reportResource = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Reports_Model_Resource_Report_Product_Viewed');
$reportResource->beginTransaction(); // prevent table truncation by incrementing the transaction nesting level counter
try {
    $reportResource->aggregate();
    $reportResource->commit();
} catch (Exception $e) {
    $reportResource->rollBack();
    throw $e;
}
