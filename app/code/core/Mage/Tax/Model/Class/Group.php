<?php
/**
 * Tax class group model
 *
 * @package     Mage
 * @subpackage  Tax
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Class_Group extends Varien_Object
{
    public function __construct($class=false)
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function getResource()
    {
        return Mage::getResourceModel('tax/class_group');
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
}