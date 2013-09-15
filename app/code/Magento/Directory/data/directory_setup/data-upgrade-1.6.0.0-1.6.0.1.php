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
 * @var \Magento\Directory\Model\Resource\Setup $installer
 */
$installer = $this;

$installer->getConnection()->insert(
    $installer->getTable('core_config_data'), array(
       'scope'    => 'default',
       'scope_id' => 0,
       'path'     => \Magento\Directory\Helper\Data::XML_PATH_DISPLAY_ALL_STATES,
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
        'path'     => \Magento\Directory\Helper\Data::XML_PATH_STATES_REQUIRED,
        'value'    => implode(',', $countries)
    )
);

