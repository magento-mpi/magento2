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
        return $this->getResource()->saveGroup($groupObject);
    }

    public function deleteGroup($groupId)
    {
        return $this->getResource()->deleteGroup($groupId);
    }
}