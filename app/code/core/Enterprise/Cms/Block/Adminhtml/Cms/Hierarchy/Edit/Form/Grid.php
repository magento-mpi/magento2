<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms Hierarchy Pages Tree Edit Cms Page Grid Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Form_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize Grid block
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setRowClickCallback('hierarchyNodes.pageGridRowClick.bind(hierarchyNodes)');
        $this->setDefaultSort('page_id');
        $this->setMassactionIdField('page_id');
        $this->setUseAjax(true);
        $this->setId('cms_page_grid');
    }

    /**
     * Prepare Cms Page Collection for Grid
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Tab_Pages_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Mage_Cms_Model_Page')->getCollection();

        $store = $this->_getStore();
        if ($store->getId()) {
            $collection->addStoreFilter($store);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Tab_Pages_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('is_selected', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'align'             => 'center',
            'index'             => 'page_id',
            'filter'            => false
        ));
        $this->addColumn('page_id', array(
            'header'            => Mage::helper('Enterprise_Cms_Helper_Data')->__('Page ID'),
            'sortable'          => true,
            'width'             => '60px',
            'type'              => 'range',
            'index'             => 'page_id'
        ));

        $this->addColumn('title', array(
            'header'            => Mage::helper('Enterprise_Cms_Helper_Data')->__('Title'),
            'index'             => 'title',
            'column_css_class'  => 'label'
        ));

        $this->addColumn('identifier', array(
            'header'            => Mage::helper('Enterprise_Cms_Helper_Data')->__('URL Key'),
            'index'             => 'identifier',
            'column_css_class'  => 'identifier'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve Grid Reload URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/pageGrid', array('_current' => true));
    }

    /**
     * Get selected store by store id passed through query.
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
}
