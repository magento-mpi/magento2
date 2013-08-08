<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*
 * @var $installer Magento_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->getConnection()->insert(
    $installer->getTable('core_config_data'), array(
       'scope'    => 'default',
       'scope_id' => 0,
       'path'     => Mage_Directory_Helper_Data::XML_PATH_DISPLAY_ALL_STATES,
       'value'    => 1
    )
);

/**
 * @var $countries array
 */
$countries = array();
foreach(Mage::helper('Mage_Directory_Helper_Data')->getCountryCollection() as $country) {
    if($country->getRegionCollection()->getSize() > 0) {
        $countries[] = $country->getId();
    }
}

$installer->getConnection()->insert(
    $installer->getTable('core_config_data'), array(
        'scope'    => 'default',
        'scope_id' => 0,
        'path'     => Mage_Directory_Helper_Data::XML_PATH_STATES_REQUIRED,
        'value'    => implode(',', $countries)
    )
);

