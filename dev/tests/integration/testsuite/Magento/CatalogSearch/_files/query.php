<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var $query \Magento\CatalogSearch\Model\Query */
$query = $objectManager->create('Magento\CatalogSearch\Model\Query');
$query->setStoreId(1);
$query->setQueryText(
    'query_text'
)->setNumResults(
    1
)->setPopularity(
    1
)->setDisplayInTerms(
    1
)->setIsActive(
    1
)->setIsProcessed(
    1
)->save();
