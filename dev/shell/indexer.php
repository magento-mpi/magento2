<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shell
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../app/bootstrap.php';
$params = array(
    \Magento\Core\Model\App::PARAM_RUN_CODE => 'admin',
    \Magento\Core\Model\App::PARAM_RUN_TYPE => 'store',
);

$entryPoint = new \Magento\Index\Model\EntryPoint\Shell(
    basename(__FILE__),
    new \Magento\Index\Model\EntryPoint\Shell\ErrorHandler(),
    new \Magento\Core\Model\Config\Primary(BP, $params)
);
$entryPoint->processRequest();
