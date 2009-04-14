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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Staging_Model_Mysql4_Dataset extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('enterprise_staging/dataset', 'dataset_id');
    }

    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $removedItems = $object->getRemovedItems();
        if ($removedItems) {
            foreach ($removedItems as $item) {
                /* @var $entity Enterprise_Staging_Model_Dataset_Item */
                $item->delete();
            }
        }
        return parent::_afterSave($object);
    }

    public function validate($object, $name)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable())
            ->where("name=?", $name);

        if ($object->getId()) {
            $select->where("dataset_id!=?", $object->getId());
        }

        if (!$read->fetchOne($select)) {
           return true;
        }

        return false;
    }
}