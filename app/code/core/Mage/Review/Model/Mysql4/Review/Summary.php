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
 * @category   Mage
 * @package    Mage_Review
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review summary resource model
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Review_Model_Mysql4_Review_Summary extends Mage_Core_Model_Mysql4_Abstract
{
    public function __construct()
    {
        $this->_init('review/review_aggregate', 'entity_pk_value');
    }

    protected function _getLoadSelect($field, $value, $storeId=0)
    {
        $read = $this->getConnection('read');

	   	$select = $read->select()
            ->from($this->getMainTable())
            ->where('store_id = ?', $storeId)
            ->where($field.'=?', $value);
        return $select;
    }

    /**
     * Load an object
     *
     * @param Varien_Object $object
     * @param integer $id
     * @param string $field field to load by (defaults to model id)
     * @return boolean
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        if (is_null($field)) {
            $field = $this->getIdFieldName();
        }

        $read = $this->getConnection('read');
        if (!$read) {
            return false;
        }

        $select = $this->_getLoadSelect($field, $value, $object->getStoreId());
        $data = $read->fetchRow($select);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);

        return true;
    }
}