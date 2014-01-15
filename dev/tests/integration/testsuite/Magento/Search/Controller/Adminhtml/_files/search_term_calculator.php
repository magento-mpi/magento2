<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var Magento\Core\Model\StoreManagerInterface $storeManager */
$storeManager = $objectManager->get('Magento\Core\Model\StoreManagerInterface');

/** @var Magento\CatalogSearch\Model\Query $model */
$model = $objectManager->create('Magento\CatalogSearch\Model\Query');
$model->setData(array(
    'query_text' => 'Calculator',
    'synonym_for' => 'Curculator',
    'is_active' => true,
    'store_id' => $storeManager->getStore()->getId(),
));
$model->save();
