<?php
/**
 * Product visibility model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Product_Visibility extends Varien_Object 
{
    const VISIBILITY_NOT_VISIBLE    = 1;
    const VISIBILITY_IN_CATALOG     = 2;
    const VISIBILITY_IN_SEARCH      = 3;
    const VISIBILITY_BOTH           = 4;
    
    public function addVisibleInCatalogFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        $collection->addAttributeToFilter('visibility', array('in'=>$this->getVisibleInCatalogIds()));
        return $this;
    }
    
    public function addVisibleInSearchFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        $collection->addAttributeToFilter('visibility', array('in'=>$this->getVisibleInSearchIds()));
        return $this;
    }
    
    public function getVisibleInCatalogIds()
    {
        return array(self::VISIBILITY_IN_CATALOG, self::VISIBILITY_BOTH);
    }

    public function getVisibleInSearchIds()
    {
        return array(self::VISIBILITY_IN_SEARCH, self::VISIBILITY_BOTH);
    }
}
