<?php
/**
 * Tax class model
 *
 * @package     Mage
 * @subpackage  Tax
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Class extends Varien_Object
{
    public function __construct($class=false)
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function getResource()
    {
        return Mage::getResourceModel('tax/class');
    }

    public function load($classId)
    {
        $this->getResource()->load($this, $classId);
        return $this;
    }

    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }

    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }

    public function getCustomerGroupCollection()
    {
        return Mage::getResourceModel('customer/group_collection');
    }

    public function itemExists()
    {
        return $this->getResource()->itemExists($this);
    }
}