<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms Pages Tree Edit Form Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Currently selected store in store switcher
     * @var null|int
     */
    protected $_currentStore = null;

    /**
     * Page lock flag
     *
     * @var null|bool
     */
    protected $_isLocked = null;

    /**
     * Define custom form template for block
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('enterprise/cms/hierarchy/edit.phtml');
        $this->_currentStore = $this->getRequest()->getParam('store');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save'),
            'method'    => 'post'
        ));

        /*
         * Define general properties for each node
         */
        $fieldset   = $form->addFieldset('node_properties_fieldset', array(
            'legend'    => Mage::helper('enterprise_cms')->__('Page Properties')
        ));

        $fieldset->addField('nodes_data', 'hidden', array(
            'name'      => 'nodes_data'
        ));

        $fieldset->addField('removed_nodes', 'hidden', array(
            'name'      => 'removed_nodes'
        ));

        $fieldset->addField('node_id', 'hidden', array(
            'name'      => 'node_id'
        ));

        $fieldset->addField('node_page_id', 'hidden', array(
            'name'      => 'node_page_id'
        ));

        $fieldset->addField('node_label', 'text', array(
            'name'      => 'label',
            'label'     => Mage::helper('enterprise_cms')->__('Title'),
            'required'  => true,
            'onchange'   => 'hierarchyNodes.nodeChanged()',
            'tabindex'   => '10'
        ));

        $fieldset->addField('node_identifier', 'text', array(
            'name'      => 'identifier',
            'label'     => Mage::helper('enterprise_cms')->__('URL Key'),
            'required'  => true,
            'class'     => 'validate-identifier',
            'onchange'   => 'hierarchyNodes.nodeChanged()',
            'tabindex'   => '20'
        ));

        $fieldset->addField('node_label_text', 'note', array(
            'label'     => Mage::helper('enterprise_cms')->__('Title')
        ));

        $fieldset->addField('node_identifier_text', 'note', array(
            'label'     => Mage::helper('enterprise_cms')->__('URL Key')
        ));

        $fieldset->addField('node_preview', 'link', array(
            'label'     => Mage::helper('enterprise_cms')->__('Preview'),
            'href'      => '#',
            'value'     => Mage::helper('enterprise_cms')->__('No preview available'),
        ));

        /*
         * Define field set with elements for root nodes
         */
        if (Mage::helper('enterprise_cms/hierarchy')->isMetadataEnabled()) {
            $fieldset   = $form->addFieldset('metadata_fieldset', array(
                'legend'    => Mage::helper('enterprise_cms')->__('Render Metadata in HTML Head')
            ));

            $yesNoOptions = Mage::getSingleton('adminhtml/system_config_source_yesno')
                    ->toOptionArray();

            $fieldset->addField('meta_first_last', 'select', array(
                'label'     => Mage::helper('enterprise_cms')->__('First'),
                'title'     => Mage::helper('enterprise_cms')->__('First'),
                'name'      => 'meta_first_last',
                'values'   => $yesNoOptions,
                'onchange'   => 'hierarchyNodes.nodeChanged()',
                'tabindex'   => '30'
            ));

            $fieldset->addField('meta_next_previous', 'select', array(
                'label'     => Mage::helper('enterprise_cms')->__('Next/Previous'),
                'title'     => Mage::helper('enterprise_cms')->__('Next/Previous'),
                'name'      => 'meta_next_previous',
                'values'   => $yesNoOptions,
                'onchange'   => 'hierarchyNodes.nodeChanged()',
                'tabindex'   => '40'
            ));


    // commented bc of changes in road map
    //        $fieldset->addField('meta_chapter', 'select', array(
    //            'label'     => Mage::helper('enterprise_cms')->__('Chapter'),
    //            'title'     => Mage::helper('enterprise_cms')->__('Chapter'),
    //            'name'      => 'meta_chapter',
    //            'values'   => $yesNoOptions,
    //            'onchange'   => 'hierarchyNodes.nodeChanged()',
    //            'tabindex'   => '50'
    //        ));
    //
    //        $fieldset->addField('meta_section', 'select', array(
    //            'label'     => Mage::helper('enterprise_cms')->__('Section'),
    //            'title'     => Mage::helper('enterprise_cms')->__('Section'),
    //            'name'      => 'meta_section',
    //            'values'   => $yesNoOptions,
    //            'onchange'   => 'hierarchyNodes.nodeChanged()',
    //            'tabindex'   => '60'
    //        ));
        }

        $pagerFieldset   = $form->addFieldset('pager_fieldset', array(
            'legend'    => Mage::helper('enterprise_cms')->__('Pagination Options for Nested Pages')
        ));

        $pagerFieldset->addField('pager_visibility', 'select', array(
            'label'     => Mage::helper('enterprise_cms')->__('Enable Pagination'),
            'name'      => 'pager_visibility',
            'values'    => Mage::getSingleton('enterprise_cms/source_hierarchy_visibility')->toOptionArray(),
            'value'     => Enterprise_Cms_Helper_Hierarchy::METADATA_VISIBILITY_PARENT,
            'onchange'  => "hierarchyNodes.metadataChanged('pager_visibility', 'pager_fieldset')",
            'tabindex'  => '70'
        ));
        $pagerFieldset->addField('pager_frame', 'text', array(
            'name'      => 'pager_frame',
            'label'     => Mage::helper('enterprise_cms')->__('Frame'),
            'onchange'  => 'hierarchyNodes.nodeChanged()',
            'container_id' => 'field_pager_frame',
            'note'      => Mage::helper('enterprise_cms')->__('How many Links to display at once'),
            'tabindex'  => '80'
        ));
        $pagerFieldset->addField('pager_jump', 'text', array(
            'name'      => 'pager_jump',
            'label'     => Mage::helper('enterprise_cms')->__('Frame Skip'),
            'onchange'  => 'hierarchyNodes.nodeChanged()',
            'container_id' => 'field_pager_jump',
            'note'      => Mage::helper('enterprise_cms')->__('If the Current Frame Position does not cover Utmost Pages, will render Link to Current Position plus/minus this Value'),
            'tabindex'  => '90'
        ));

        $menuFieldset   = $form->addFieldset('menu_fieldset', array(
            'legend'    => Mage::helper('enterprise_cms')->__('Navigation Menu Options')
        ));


        $menuVisibilitySource = Mage::getSingleton('enterprise_cms/source_hierarchy_visibility')->toOptionArray();
        $menuVisibilitySelfSource = $menuVisibilitySource;
        unset($menuVisibilitySelfSource[Enterprise_Cms_Helper_Hierarchy::METADATA_VISIBILITY_YES]);

        $menuFieldset->addField('menu_visibility_self', 'select', array(
            'label'     => Mage::helper('enterprise_cms')->__('This Page Navigation Menu'),
            'name'      => 'menu_visibility_self',
            'values'    => $menuVisibilitySelfSource,
            'value'     => Enterprise_Cms_Helper_Hierarchy::METADATA_VISIBILITY_PARENT,
            'onchange'   => "hierarchyNodes.nodeChanged()",
            'container_id' => 'field_menu_visibility_self',
            'tabindex'  => '100'
        ));

        $menuFieldset->addField('menu_visibility', 'select', array(
            'label'     => Mage::helper('enterprise_cms')->__('Navigation Menu for Nested Pages'),
            'name'      => 'menu_visibility',
            'values'    => $menuVisibilitySource,
            'value'     => Enterprise_Cms_Helper_Hierarchy::METADATA_VISIBILITY_PARENT,
            'onchange'   => "hierarchyNodes.metadataChanged('menu_visibility', 'menu_fieldset')",
            'container_id' => 'field_menu_visibility',
            'tabindex'  => '110'
        ));
        $menuFieldset->addField('menu_levels_up', 'text', array(
            'name'      => 'menu_levels_up',
            'label'     => Mage::helper('enterprise_cms')->__('Levels Up'),
            'onchange'  => 'hierarchyNodes.nodeChanged()',
            'container_id' => 'field_menu_levels_up',
            'note'      => Mage::helper('enterprise_cms')->__('Number of Parent Node Levels to Include'),
            'tabindex'  => '120'
        ));
        $menuFieldset->addField('menu_levels_down', 'text', array(
            'name'      => 'menu_levels_down',
            'label'     => Mage::helper('enterprise_cms')->__('Levels Down'),
            'onchange'  => 'hierarchyNodes.nodeChanged()',
            'container_id' => 'field_menu_levels_down',
            'note'      => Mage::helper('enterprise_cms')->__('Number of Child Node Levels to Include'),
            'tabindex'  => '130'
        ));
        $menuFieldset->addField('menu_ordered', 'select', array(
            'label'     => Mage::helper('enterprise_cms')->__('List Type'),
            'title'     => Mage::helper('enterprise_cms')->__('List Type'),
            'name'      => 'menu_ordered',
            'values'    => Mage::getSingleton('enterprise_cms/source_hierarchy_menu_listtype')->toOptionArray(),
            'onchange'  => 'hierarchyNodes.menuListTypeChanged()',
            'container_id' => 'field_menu_ordered',
            'tabindex'  => '140'
        ));
        $menuFieldset->addField('menu_list_type', 'select', array(
            'label'     => Mage::helper('enterprise_cms')->__('List Style'),
            'title'     => Mage::helper('enterprise_cms')->__('List Style'),
            'name'      => 'menu_list_type',
            'values'    => Mage::getSingleton('enterprise_cms/source_hierarchy_menu_listmode')->toOptionArray(),
            'onchange'  => 'hierarchyNodes.nodeChanged()',
            'container_id' => 'field_menu_list_type',
            'tabindex'  => '150'
        ));

        if ($this->isLockedByOther()) {
            foreach ($form->getElements() as $formElement) {
                if ($formElement->getType() == 'fieldset') {
                    foreach ($formElement->getElements() as $fieldsetElement) {
                        $fieldsetElement->setDisabled(true);
                    }
                }
            }
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve buttons HTML for Cms Page Grid
     *
     * @return string
     */
    public function getPageGridButtonsHtml()
    {
        $addButtonData = array(
            'id'        => 'add_cms_pages',
            'label'     => Mage::helper('enterprise_cms')->__('Add Selected Page(s) to Tree'),
            'onclick'   => 'hierarchyNodes.pageGridAddSelected()',
            'class'     => 'add' . (($this->isLockedByOther()) ? ' disabled' : ''),
            'disabled'  => $this->isLockedByOther()
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData($addButtonData)->toHtml();
    }

    /**
     * Retrieve Buttons HTML for Page Properties form
     *
     * @return string
     */
    public function getPagePropertiesButtons()
    {
        $buttons = array();
        $buttons[] = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'id'        => 'delete_node_button',
            'label'     => Mage::helper('enterprise_cms')->__('Remove From Tree'),
            'onclick'   => 'hierarchyNodes.deleteNodePage()',
            'class'     => 'delete' . (($this->isLockedByOther()) ? ' disabled' : ''),
            'disabled'  => $this->isLockedByOther()
        ))->toHtml();
        $buttons[] = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'id'        => 'cancel_node_button',
            'label'     => Mage::helper('enterprise_cms')->__('Cancel'),
            'onclick'   => 'hierarchyNodes.cancelNodePage()',
            'class'     => 'delete' . (($this->isLockedByOther()) ? ' disabled' : ''),
            'disabled'  => $this->isLockedByOther()
        ))->toHtml();
        $buttons[] = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'id'        => 'save_node_button',
            'label'     => Mage::helper('enterprise_cms')->__('Save'),
            'onclick'   => 'hierarchyNodes.saveNodePage()',
            'class'     => 'save' . (($this->isLockedByOther()) ? ' disabled' : ''),
            'disabled'  => $this->isLockedByOther()
        ))->toHtml();

        return join(' ', $buttons);
    }

    /**
     * Retrieve buttons HTML for Pages Tree
     *
     * @return string
     */
    public function getTreeButtonsHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'id'        => 'new_node_button',
            'label'     => Mage::helper('enterprise_cms')->__('Add Node...'),
            'onclick'   => 'hierarchyNodes.newNodePage()',
            'class'     => 'add' . (($this->isLockedByOther()) ? ' disabled' : ''),
            'disabled'  => $this->isLockedByOther()
        ))->toHtml();
    }

    /**
     * Retrieve current nodes Json basing on data loaded from
     * DB or from model in case we had error in save process.
     *
     * @return string
     */
    public function getNodesJson()
    {
        $nodes = array();
        /* @var $node Enterprise_Cms_Model_Hierarchy_Node */
        $nodeModel = Mage::registry('current_hierarchy_node');
        // restore data is exists
        $data = Mage::helper('core')->jsonDecode($nodeModel->getNodesData());
        if (is_array($data)) {
            foreach ($data as $v) {
                $node = array(
                    'node_id'               => $v['node_id'],
                    'parent_node_id'        => $v['parent_node_id'],
                    'label'                 => $v['label'],
                    'identifier'            => $v['identifier'],
                    'page_id'               => empty($v['page_id']) ? null : $v['page_id']
                );
                $nodes[] = Mage::helper('enterprise_cms/hierarchy')->copyMetaData($v, $node);
            }
        } else {
            $collection = $nodeModel->getCollection()
                ->joinCmsPage()
                ->addCmsPageInStoresColumn()
                ->joinMetaData()
                ->setOrderByLevel();

            foreach ($collection as $item) {
                /* @var $item Enterprise_Cms_Model_Hierarchy_Node */
                $node = array(
                    'node_id'               => $item->getId(),
                    'parent_node_id'        => $item->getParentNodeId(),
                    'label'                 => $item->getLabel(),
                    'identifier'            => $item->getIdentifier(),
                    'page_id'               => $item->getPageId(),
                    'assigned_to_store'     => $this->isNodeAvailableForStore($item, $this->_currentStore)
                );
                $nodes[] = Mage::helper('enterprise_cms/hierarchy')->copyMetaData($item->getData(), $node);
            }
        }

        return Mage::helper('core')->jsonEncode($nodes);
    }

    /**
     * Check if passed node available for store in case this node representation of page.
     * If node does not represent page then method will return true.
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $node
     * @param null|int $store
     * @return bool
     */
    public function isNodeAvailableForStore($node, $store)
    {
        if (!$node->getPageId()) {
            return true;
        }

        if (!$store) {
            return true;
        }

        if ($node->getPageInStores() == '0') {
            return true;
        }

        $stores = explode(',', $node->getPageInStores());
        if (in_array($store, $stores)) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve Grid JavaScript object name
     *
     * @return string
     */
    public function getGridJsObject()
    {
        return $this->getParentBlock()->getChild('cms_page_grid')->getJsObjectName();
    }

    /**
     * Prepare translated label 'Save' for button used in Js.
     *
     * @return string
     */
    public function getButtonSaveLabel()
    {
        return Mage::helper('enterprise_cms')->__('Add To Tree');
    }

    /**
     * Prepare translated label 'Update' for button used in Js
     *
     * @return string
     */
    public function getButtonUpdateLabel()
    {
        return Mage::helper('enterprise_cms')->__('Update');
    }

    /**
     * Return legend for Hierarchy node fieldset
     *
     * @return string
     */
    public function getNodeFieldsetLegend()
    {
        return Mage::helper('enterprise_cms')->__('Node Properties');
    }

    /**
     * Return legend for Hierarchy page fieldset
     *
     * @return string
     */
    public function getPageFieldsetLegend()
    {
        return Mage::helper('enterprise_cms')->__('Page Properties');
    }

    /**
     * Getter for protected _currentStore
     *
     * @return null|int
     */
    public function getCurrentStore()
    {
        return $this->_currentStore;
    }

    /**
     * Return URL query param for current store
     *
     * @return string
     */
    public function getCurrentStoreUrlParam()
    {
        /* @var $store Mage_Core_Model_Store */
        $store = $this->_currentStore ? Mage::app()->getStore($this->_currentStore) : Mage::app()->getAnyStoreView();
        return '?___store=' . $store->getCode();
    }

    /**
     * Return Base URL for current Store
     *
     * @return string
     */
    public function getStoreBaseUrl()
    {
        /* @var $store Mage_Core_Model_Store */
        $store = $this->_currentStore ? Mage::app()->getStore($this->_currentStore) : Mage::app()->getAnyStoreView();
        return $store->getBaseUrl();
    }

    /**
     * Retrieve html of store switcher added from layout
     *
     * @return string
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getLayout()->getBlock('store_switcher')
            ->setUseConfirm(false)
            ->toHtml();
    }

    /**
     * Return List styles separately for unordered/ordererd list as json
     *
     * @return string
     */
    public function getListModesJson()
    {
        $listModes = Mage::getSingleton('enterprise_cms/source_hierarchy_menu_listmode')->toOptionArray();
        $result = array();
        foreach ($listModes as $type => $label) {
            if ($type == '') {
                continue;
            }
            $listType = in_array($type, array('circle', 'disc', 'square')) ? '0' : '1';
            $result[$listType][$type] = $label;
        }

        return Mage::helper('core')->jsonEncode($result);
    }

    /**
     * Check whether current user can drag nodes
     *
     * @return bool
     */
    public function canDragNodes()
    {
        return !$this->isLockedByOther();
    }

    /**
     * Check whether page is locked by other user
     *
     * @return bool
     */
    public function isLockedByOther()
    {
        if (is_null($this->_isLocked)) {
            $this->_isLocked = Mage::getModel('enterprise_cms/hierarchy_lock', Mage::getSingleton('admin/session'))
                ->isLockedByOther();
        }
        Mage::log($this->_isLocked);
        return $this->_isLocked;
    }
}
