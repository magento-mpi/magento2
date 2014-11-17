<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\GiftRegistry\Model\Resource\Setup */
$installer = $this;

$defaultTypes = array('1' => 'Birthday', '2' => 'Baby Registry', '3' => 'Wedding');
foreach ($defaultTypes as $typeId => $label) {
    $installer->getConnection()->update(
        $this->getTable('magento_giftregistry_type_info'),
        array('store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID),
        array(
            'type_id = ?' => $typeId,
            'store_id = ?' => \Magento\Store\Model\Store::DISTRO_STORE_ID,
            'label = ?' => $label
        )
    );
}
