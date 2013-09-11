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
$objectManager->configure(array(
    'preferences' => array(
        'Magento_Core_Controller_Request_Http' => 'Magento_Test_Request',
    )
));

/** @var $query Magento_CatalogSearch_Model_Query */
$query = $objectManager->create('Magento_CatalogSearch_Model_Query');
$query->setStoreId(1);
$query
    ->setQueryText('query_text')
    ->setNumResults(1)
    ->setPopularity(1)
    ->setDisplayInTerms(1)
    ->setIsActive(1)
    ->setIsProcessed(1)
    ->save();
