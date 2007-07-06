<?php
/**
 * Tax customers class model
 *
 * @package     Mage
 * @subpackage  Tax
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Class_Customer extends Varien_Object
{
    public function getResource()
    {
        return Mage::getResourceModel('tax/class_customer');
    }

    public function load($classId)
    {
        return $this->getResource()->load($classId);
    }

    public function save($classObject)
    {
        return $this->getResource()->save($classObject);
    }

    public function delete($classObject)
    {
        return $this->getResource()->delete($classObject);
    }

    public function saveGroup($groupObject)
    {
        return $this->getResource()->addGroup($groupObject);
    }

    public function deleteGroup($groupObject)
    {
        return $this->getResource()->deleteGroup($groupObject);
    }
}