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
 * @package    Enterprise_CustomerSegment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * CustomerSegment data resource model
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 */
class Enterprise_CustomerSegment_Model_Mysql4_Segment extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Intialize resource model
     *
     * @return void
     */
    protected function _construct ()
    {
        $this->_init('enterprise_customersegment/segment', 'segment_id');
    }

    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $id = $object->getId();

        $condition = $this->_getWriteAdapter()->quoteInto("segment_id = ?", $id);
        $this->_getWriteAdapter()->delete($this->getTable('enterprise_customersegment/event'), $condition);
        if ($object->getValidationEvents() && is_array($object->getValidationEvents())) {
            foreach ($object->getValidationEvents() as $event) {
                $data = array(
                    'segment_id' => $id,
                    'event'      => $event,
                );

                $this->_getWriteAdapter()->insert($this->getTable('enterprise_customersegment/event'), $data);
            }
        }

        return parent::_afterSave($object);
    }

    public function runConditionSql($sql)
    {
        return $this->_getReadAdapter()->fetchOne($sql) == 1;
    }

    public function createSelect()
    {
        return $this->_getReadAdapter()->select();
    }

    public function quoteInto($string, $param)
    {
        return $this->_getReadAdapter()->quoteInto($string, $param);
    }
}
