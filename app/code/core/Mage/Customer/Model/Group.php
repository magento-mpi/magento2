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
class Mage_Customer_Model_Group extends Varien_Object
{
    /**
     * Alias for setCustomerGroupId
     * @param int $value
     */
    public function setId($value)
    {
        return $this->setCustomerGroupId($value);
    }

    /**
     * Alias for getCustomerGroupId
     * @return int
     */
    public function getId()
    {
        return $this->getCustomerGroupId();
    }

    /**
     * Alias for setCustomerGroupCode
     *
     * @param string $value
     */
    public function setCode($value)
    {
        return $this->setCustomerGroupCode($value);
    }

    /**
     * Alias for getCustomerGroupCode
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getCustomerGroupCode();
    }

    public function getResource()
    {
        return Mage::getResourceSingleton('customer/group');
    }

    /**
     * Load group info by id
     *
     * @param   int $groupId
     * @return  Mage_Customer_Model_Group
     */
    public function load($groupId)
    {
        $this->setData($this->getResource()->load($groupId));
        return $this;
    }

    /**
     * Save group
     *
     * @return  Mage_Customer_Model_Group
     * @throws  Mage_Customer_Exception
     */
    public function save()
    {
        if( $this->_itemExists() ) {
            throw new Exception(__('Customer group already exists.'));
        }
        $this->getResource()->save($this);
        return $this;
    }

    /**
     * Delete group
     *
     * @return  Mage_Customer_Model_Group
     * @throws  Mage_Customer_Exception
     */
    public function delete()
    {
        $this->getResource()->delete($this->getId());
        return $this;
    }

    protected function _itemExists()
    {
        return $this->getResource()->itemExists($this);
    }
}