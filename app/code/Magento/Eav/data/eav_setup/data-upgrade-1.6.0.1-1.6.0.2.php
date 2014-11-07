<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Eav\Model\Entity\Setup */
$installer = $this;
$installer->startSetup();
/** @var $groups \Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection*/
$groups = $installer->getAttributeGroupCollectionFactory();
foreach ($groups as $group) {
    /** @var $group \Magento\Eav\Model\Entity\Attribute\Group*/
    $group->save();
}

$installer->endSetup();
