<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Core Design Resource Model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Design extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('core/design_change', 'design_change_id');
    }

    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        foreach (array('date_from', 'date_to') as $field) {
            $object->setData($field, $this->formatDate($object->getData($field)));
        }

        parent::_beforeSave($object);
    }

    /**
     * Load design one change for store and date
     *
     * @param int $storeId
     * @param int|string|Zend_Date $date
     * @return array|false
     */
    public function loadChange($storeId, $date = null)
    {
        if (is_null($date)) {
            $date = time();
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('store_id = ?', $storeId)
            ->where('(date_from <= ? OR date_from IS NULL)', $this->formatDbDate($date, false))
            ->where('(date_to >= ? OR date_to IS NULL)', $this->formatDbDate($date, false))
            ->limit(1);

        return $this->_getReadAdapter()->fetchRow($select);
    }
}
