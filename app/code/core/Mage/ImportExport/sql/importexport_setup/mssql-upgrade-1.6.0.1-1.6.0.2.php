<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_ImportExport_Model_Resource_Setup */
$installer = $this;

$installFile = dirname(__FILE__) . DS . 'mysql4-upgrade-1.6.0.1-1.6.0.2.php';
if (file_exists($installFile)) {
    include $installFile;
}
