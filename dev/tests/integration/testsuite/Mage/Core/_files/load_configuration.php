<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

$areaConfig = Mage::getModel(
    'Mage_Core_Model_Config_Base',
    array('sourceData' => dirname(__FILE__).'/etc/config.xml')
);
Mage::app()->getConfig()->extend($areaConfig);
