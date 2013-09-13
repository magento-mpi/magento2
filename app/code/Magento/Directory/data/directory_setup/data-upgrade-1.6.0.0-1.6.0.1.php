<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @var Magento_Directory_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->getConnection()->insert(
    $installer->getTable('core_config_data'), array(
       'scope'    => 'default',
       'scope_id' => 0,
       'path'     => Magento_Directory_Helper_Data::XML_PATH_DISPLAY_ALL_STATES,
       'value'    => 1
    )
);

/**
 * @var $countries array
 */
$countries = array();
foreach($installer->getDirectoryData()->getCountryCollection() as $country) {
    if($country->getRegionCollection()->getSize() > 0) {
        $countries[] = $country->getId();
    }
}

$installer->getConnection()->insert(
    $installer->getTable('core_config_data'), array(
        'scope'    => 'default',
        'scope_id' => 0,
        'path'     => Magento_Directory_Helper_Data::XML_PATH_STATES_REQUIRED,
        'value'    => implode(',', $countries)
    )
);

