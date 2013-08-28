<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Enterprise_GiftRegistry_Model_Resource_Setup */
$installer = $this;

$defaultTypes = array(
    '1' => 'Birthday',
    '2' => 'Baby Registry',
    '3' => 'Wedding'
);
foreach ($defaultTypes as $typeId => $label) {
    $installer->getConnection()->update(
        $this->getTable('enterprise_giftregistry_type_info'),
        array('store_id' => Magento_Core_Model_AppInterface::ADMIN_STORE_ID),
        array(
            'type_id = ?' => $typeId,
            'store_id = ?' => Magento_Core_Model_AppInterface::DISTRO_STORE_ID,
            'label = ?' => $label
        )
    );
}
