<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * @var $installer \Magento\Directory\Model\Resource\Setup
 */
$installer = $this;

$installer->getConnection()->insert(
    $installer->getTable('core_config_data'),
    [
        'scope' => 'default',
        'scope_id' => 0,
        'path' => \Magento\Directory\Helper\Data::XML_PATH_DISPLAY_ALL_STATES,
        'value' => 1
    ]
);

/**
 * @var $countries array
 */
$countries = [];
foreach ($installer->getDirectoryData()->getCountryCollection() as $country) {
    if ($country->getRegionCollection()->getSize() > 0) {
        $countries[] = $country->getId();
    }
}

$installer->getConnection()->insert(
    $installer->getTable('core_config_data'),
    [
        'scope' => 'default',
        'scope_id' => 0,
        'path' => \Magento\Directory\Helper\Data::XML_PATH_STATES_REQUIRED,
        'value' => implode(',', $countries)
    ]
);
