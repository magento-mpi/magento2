<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Shell
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../app/bootstrap.php';
$params = array(
    Mage::PARAM_RUN_CODE => 'admin',
    Mage::PARAM_RUN_TYPE => 'store',
);
$entryPoint = new \Magento\Log\Model\EntryPoint\Shell(
    new \Magento\Core\Model\Config\Primary(BP, $params),
    basename(__FILE__)
);
$entryPoint->processRequest();
