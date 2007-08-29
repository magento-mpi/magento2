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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer group model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Customer_Model_Entity_Group
{
    /**
     * Customers group table name
     *
     * @var string
     */
    protected $_groupTable;

    /**
     * DB read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * DB write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;

    /**
     * Constructor
     *
     * Model initialization
     */
    public function __construct()
    {
        $this->_groupTable = Mage::getSingleton('core/resource')->getTableName("customer/customer_group");
        $this->_read = Mage::getSingleton('core/resource')->getConnection('customer_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('customer_write');
    }

    /**
     * Load group information from DB
     *
     * @param   int $groupId
     * @return  array
     */
    public function load($groupId)
    {
        $select = $this->_read->select()->from($this->_groupTable)
            ->where('customer_group_id=?',$groupId);

        return $this->_read->fetchRow($select);
    }

    /**
     * Save group information to DB
     *
     * @param   Mage_Customer_Model_Group $group
     * @return  Mage_Customer_Model_Group
     * @throws  Mage_Customer_Exception
     */
    public function save(Mage_Customer_Model_Group $group)
    {
        $this->_write->beginTransaction();

        try {
            $data = $this->_prepareSaveInformation($group);
            if ($group->getId())  {
                $this->_write->update($this->_groupTable, $data,
                                      $this->_write->quoteInto('customer_group_id=?', $group->getId()));
            } else {
                $this->_write->insert($this->_groupTable, $data);
                $group->setId($this->_write->lastInsertId());
            }
            $this->_write->commit();
        }
        catch(Mage_Core_Exception $e) {
            throw $e;
        }
        catch(Exception $e) {
            $this->_write->rollBack();
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE024'));
        }
        return $group;
    }

    /**
     * Prepares group information for saving
     *
     * @param   Mage_Customer_Model_Group $group
     * @return  array
     * @throws  Mage_Customer_Exception
     */
    protected function _prepareSaveInformation(Mage_Customer_Model_Group $group)
    {
        $data = array();
        $data['customer_group_id'] = $group->getId();
        $data['customer_group_code'] = $group->getCode();
        $data['tax_class_id']   = $group->getTaxClassId();

        if (trim($group->getCode()) == '') {
            throw Mage::exception('Mage_Customer')
                ->addMessage(Mage::getModel('customer/message')->error('CSTE025'));
        }
        return $data;
    }

    /**
     * Delete customer group from DB.
     *
     * @param   int $groupId
     * @throws  Mage_Customer_Exception
     */
    public function delete($groupId)
    {
        $groupId = (int)$groupId;
        if (!$groupId) {
            throw Mage::exception('Mage_Customer')
                ->addMessage(Mage::getModel('customer/message')->error('CSTE026'));
        }

        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->_groupTable, $this->_write->quoteInto('customer_group_id=?',$groupId));
            $this->_write->commit();
        }
        catch (Exception $e) {
            $this->_write->rollBack();
            throw Mage::exception('Mage_Customer')
                ->addMessage(Mage::getModel('customer/message')->error('CSTE027'));
        }
    }

    public function itemExists($object)
    {
        $select = $this->_read->select();
        $select->from($this->_groupTable)
            ->where('customer_group_code = ?', $object->getCode())
            ->where('customer_group_id != ?', $object->getId());

        $data = $this->_read->fetchRow($select);

        if( $data && count($data) > 0 ) {
            return true;
        } else {
            return false;
        }
    }
}
