<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CatalogSearch
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $query Mage_CatalogSearch_Model_Query */
$query = Mage::getModel('Mage_CatalogSearch_Model_Query');
$query->setStoreId(1);
$query
    ->setQueryText('query_text')
    ->setNumResults(1)
    ->setPopularity(1)
    ->setDisplayInTerms(1)
    ->setIsActive(1)
    ->setIsProcessed(1)
    ->save();
