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
class Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Form_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize Grid block
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setRowClickCallback('hierarchyNodes.pageGridRowClick.bind(hierarchyNodes)');
        $this->setCheckboxCheckCallback('hierarchyNodes.checkCheckboxes.bind(hierarchyNodes)');
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
        $collection = Mage::getModel('Magento_Cms_Model_Page')->getCollection();

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
            'header_css_class'  => 'col-select',
            'column_css_class'  => 'col-select',
            'type'              => 'checkbox',
            'index'             => 'page_id',
            'filter'            => false
        ));
        $this->addColumn('page_id', array(
            'header'            => __('Page ID'),
            'header_css_class'  => 'col-page-id',
            'column_css_class'  => 'col-page-id',
            'sortable'          => true,
            'type'              => 'range',
            'index'             => 'page_id'
        ));

        $this->addColumn('title', array(
            'header'            => __('Title'),
            'header_css_class'  => 'col-title',
            'column_css_class'  => 'col-title label',
            'index'             => 'title'
        ));

        $this->addColumn('identifier', array(
            'header'            => __('URL Key'),
            'header_css_class'  => 'col-identifier',
            'column_css_class'  => 'col-identifier identifier',
            'index'             => 'identifier'
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
     * @return Magento_Core_Model_Store
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
}
