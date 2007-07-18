<?php
/**
 * Category collection
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Category_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function __construct() 
    {
        $this->setEntity(Mage::getResourceSingleton('catalog/category'));
        $this->setObject('catalog/category');
    }
}
