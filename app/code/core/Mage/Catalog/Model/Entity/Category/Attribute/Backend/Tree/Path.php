<?php
/**
 * Catalog category tree_path attribute backend model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Category_Attribute_Backend_Tree_Path extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function afterSave($object)
    {
        parent::afterSave($object);
        $tree = $object->getTreeModel()
            ->load();

        $store = $this->getAttribute()->getEntity()->getStore();
        $lastNodeId = $store->getConfig('catalog/category/root_id');

        $nodeIds = array();
        $path = $tree->getPath($object->getId());
        foreach ($path as $node) {
            // $node->getLevel()<=1 - need fix
            if ($node->getId() == $lastNodeId || $node->getLevel()<=1) {
                break;
            }
            $nodeIds[] = $node->getId();
        }
        
        $object->setData($this->getAttribute()->getAttributeCode(), implode(',', $nodeIds));
        $this->getAttribute()->getEntity()
            ->saveAttribute($object, $this->getAttribute()->getAttributeCode());

        return $this;
    }
}
