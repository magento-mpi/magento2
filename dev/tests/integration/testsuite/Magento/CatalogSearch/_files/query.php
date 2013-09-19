<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

/** @var $query \Magento\CatalogSearch\Model\Query */
$query = $objectManager->create('Magento\CatalogSearch\Model\Query');
$query->setStoreId(1);
$query
    ->setQueryText('query_text')
    ->setNumResults(1)
    ->setPopularity(1)
    ->setDisplayInTerms(1)
    ->setIsActive(1)
    ->setIsProcessed(1)
    ->save();
