<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Gift registry type data resource model
 */
class Enterprise_GiftRegistry_Model_Mysql4_Type extends Enterprise_Enterprise_Model_Core_Mysql4_Abstract
{
    /**
     * Intialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('enterprise_giftregistry/type', 'type_id');
    }

    /**
     * Add store date to registry type data
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Enterprise_Model_Core_Mysql4_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('enterprise_giftregistry/info'), array(
                'scope' => 'IF(store_id = 0, \'default\', \'store\')',
                'label', 'is_listed', 'sort_order'
            ))
            ->where('type_id = ?', $object->getId())
            ->where('store_id IN (0,?)', $object->getStoreId());

        $data = $this->_getReadAdapter()->fetchAssoc($select);

        if (isset($data['store']) && is_array($data['store'])) {
            foreach ($data['store'] as $key => $value) {
                $object->setData($key, ($value !== null) ? $value : $data['default'][$key]);
                $object->setData($key.'_store', $value);
            }
        } else if (isset($data['default'])) {
            foreach ($data['default'] as $key => $value) {
                $object->setData($key, $value);
            }
        }

        return parent::_afterLoad($object);
    }

    /**
     * Perform actions after object save
     *
     * @param Varien_Object $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $infoTable = $this->getTable('enterprise_giftregistry/info');

        $this->_getWriteAdapter()->delete($infoTable, array(
            'type_id = ?' => $object->getId(),
            'store_id = ?' => $object->getStoreId()
        ));

        $this->_getWriteAdapter()->insert($infoTable, array(
            'type_id' => $object->getId(),
            'store_id' => $object->getStoreId(),
            'label' => $object->getLabel(),
            'is_listed' => $object->getIsListed(),
            'sort_order' => $object->getSortOrder()
        ));

        return parent::_afterSave($object);
    }
}
