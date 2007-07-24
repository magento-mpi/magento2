<?php
/**
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Tree_Attribute extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('catalog/product/attribute/set/main/tree/attribute.phtml');
    }

    public function getNodesUrl()
    {
        return $this->getUrl('*/catalog_product_set/jsonGroupTree');
    }

    public function getMoveUrl()
    {
        return $this->getUrl('*/catalog_product_set/move');
    }

    public function getGroupTreeJson()
    {
        $setId = Mage::getModel('eav/entity_type')
                    ->load(10)
                    ->getDefaultAttributeSetId();

        $groups = Mage::getModel('eav/entity_attribute_group')
                    ->getResourceCollection()
                    ->setAttributeSetFilter($setId)
                    ->load();

        $items = array();
        foreach( $groups as $node ) {
            $item = array();
            $item['text']= $node->getAttributeGroupName();
            $item['id']  = $node->getAttributeGroupId();
            $item['cls'] = 'folder active-category';
            $item['allowDrop'] = true;
            $item['allowDrag'] = false;

            $nodeChildren = Mage::getModel('eav/entity_attribute')
                                ->getResourceCollection()
                                ->setAttributeGroupFilter($node->getAttributeGroupId())
                                ->load();

            if ( $nodeChildren->getSize() > 0 ) {
                $item['children'] = array();
                foreach( $nodeChildren->getItems() as $child ) {
                    $tmpArr = array();
                    $tmpArr['text'] = $child->getAttributeName() . ' (' . $child->getAttributeCode() . ')';
                    $tmpArr['id']  = $child->getAttributeId();
                    $tmpArr['cls'] = 'leaf';
                    $tmpArr['allowDrop'] = false;
                    $tmpArr['allowDrag'] = true;
                    $tmpArr['leaf'] = true;

                    $item['children'][] = $tmpArr;
                }
            }

            $items[] = $item;
        }

        return Zend_Json::encode($items);
    }

    public function getAttributeTreeJson()
    {
        $attributes = Mage::getModel('eav/entity_attribute')
                            ->getResourceCollection()
                            ->setEntityTypeFilter(10)
                            //->setAttributeSetExcludeFilter(9)
                            ->load();

        $parent = array(
            'text' => __('Not Assigned Attributes'),
            'id' => 0,
            'cls' => 'leav',
            'allowDrop' => true,
            'allowDrag' => false,
        );

        $items = array();
        foreach( $attributes as $node ) {
            $item = array();
            $item['text']= $node->getAttributeName() . ' (' . $node->getAttributeCode() . ')';
            $item['id']  = $node->getAttributeId();
            $item['cls'] = 'leav';
            $item['allowDrop'] = false;
            $item['allowDrag'] = true;

            $items[] = $item;
        }
        $parent['children'] = $items;
        return Zend_Json::encode(array($parent));
    }
}