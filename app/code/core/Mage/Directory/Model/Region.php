<?php
/**
 * Region
 *
 * @package    Mage
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Model_Region extends Varien_Object 
{
    public function __construct($data=array()) 
    {
        parent::__construct($data);
        $this->setIdFieldName($this->getResource()->getIdFieldName());
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('directory/region');
    }
    
    public function load($regionId)
    {
        $this->getResource()->load($this, $regionId);
        return $this;
    }
}
