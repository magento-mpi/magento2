<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @copyright  {copyright}
 * @license    {license_link}
 */

$tilesConfig = Mage::getModel(
    'Magento_Core_Model_Config_Base',
    array('sourceData' => dirname(__FILE__) . '/etc/config.xml')
);
Mage::getConfig()->getNode()->extend($tilesConfig->getNode());
