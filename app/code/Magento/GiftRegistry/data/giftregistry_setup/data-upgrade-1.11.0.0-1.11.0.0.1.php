<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;

$defaultTypes = array(
    '1' => 'Birthday',
    '2' => 'Baby Registry',
    '3' => 'Wedding'
);
foreach ($defaultTypes as $typeId => $label) {
    $installer->getConnection()->update(
        $this->getTable('magento_giftregistry_type_info'),
        array('store_id' => \Magento\Core\Model\AppInterface::ADMIN_STORE_ID),
        array(
            'type_id = ?' => $typeId,
            'store_id = ?' => \Magento\Core\Model\AppInterface::DISTRO_STORE_ID,
            'label = ?' => $label
        )
    );
}
