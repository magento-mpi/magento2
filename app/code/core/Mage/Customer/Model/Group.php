<?
/**
 * Customer group model
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
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
    
}