<?php
/**
 * Entity type model
 *
 * @package     Mage
 * @subpackage  Core
 * @method      
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Entity_Type extends Varien_Object 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setIdFieldName('entity_type_id');
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('core/entity_type');
    }
    
    public function load($typeId)
    {
        $this->getResource()->load($this, $typeId);
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
    
    public function getAttributesTableName()
    {
        /**
         * @see  Varien_Object::__call()
         */
        if ($this->getAttributeTable()) {
            return Mage::getSingleton('core/resource')->getTableName($this->getAttributeTable());
        }
        Mage::throwException('Entity type attribute table not defined');
    }
}
