<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once 'app/bootstrap.php';

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';
/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';
/* Additional local.xml file from environment variable */
$options = array();
if (!empty($_SERVER['MAGE_LOCAL_CONFIG'])) {
    $options['local_config'] = $_SERVER['MAGE_LOCAL_CONFIG'];
}

Mage::run($mageRunCode, $mageRunType, $options);
