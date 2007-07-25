<?php
/**
 * @package     Mage
 * @subpackage  Admihtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('catalog/product/attribute/set/main.phtml');
    }

    protected function _initChildren()
    {
        $setId = $this->_getSetId();

        $this->setChild('group_tree',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_tree_group')
        );

        $this->setChild('new_group_form',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_formgroup')
        );

        $this->setChild('delete_group_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                                                        ->setData(array(
                                                            'label'     => __('Delete Selected Group'),
                                                            'onclick'   => 'deleteGroup();',
                                                        ))
        );

        $this->setChild('backButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Back'),
                    'onclick'   => 'window.location.href=\''.Mage::getUrl('*/*/').'\''
                ))
        );

        $this->setChild('resetButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Reset'),
                    'onclick'   => 'window.location.reload()'
                ))
        );

        $this->setChild('saveButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save Product Set'),
                    'onclick'   => 'setForm.submit();return false;'
                ))
        );

        $this->setChild('deleteButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete Attribute Set'),
                    #'onclick'   => 'setLocation(\'' . Mage::getUrl('*/*/delete', array('id' => $setId)) . '\')'
                    'onclick'   => 'setLocation(\'#\')'
                ))
        );
    }

    public function getGroupTreeHtml()
    {
        return $this->getChildHtml('group_tree');
    }

    public function getGroupFormHtml()
    {
        return $this->getChildHtml('new_group_form');
    }

    protected function _getHeader()
    {
        return __("Edit Attribute Set '{$this->_getSetData()->getAttributeSetName()}'");
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
        $setId = $this->_getSetId();

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
        $setId = $this->_getSetId();

        $attributes = Mage::getModel('eav/entity_attribute')
                            ->getResourceCollection()
                            ->setEntityTypeFilter(Mage::registry('entityType'))
                            ->setAttributeSetExcludeFilter($setId)
                            ->load();
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
        return Zend_Json::encode($items);
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('deleteButton');
    }

    public function getDeleteGroupButton()
    {
        return $this->getChildHtml('delete_group_button');
    }

    protected function _getSetId()
    {
        return ( intval($this->getRequest()->getParam('id')) > 0 )
                    ? intval($this->getRequest()->getParam('id'))
                    : Mage::getModel('eav/entity_type')
                        ->load(Mage::registry('entityType'))
                        ->getDefaultAttributeSetId();
    }

    protected function _getSetData()
    {
        return Mage::getModel('eav/entity_attribute_set')->load( $this->_getSetId() );
    }
}