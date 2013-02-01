<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * $params = $_SERVER;
 * $params['MAGE_RUN_CODE'] = 'website2';
 * $params['MAGE_RUN_TYPE'] = 'website';
 * ...
 * new Mage_Core_Model_EntryPoint_Http(BP, $params)
 * --------------------------------------------
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require __DIR__ . '/app/bootstrap.php';
$entryPoint = new Mage_Core_Model_EntryPoint_Http(BP, $_SERVER);
$entryPoint->processRequest();
