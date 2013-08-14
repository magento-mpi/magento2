<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Search query relations edit grid
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Block_Adminhtml_Search_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     *
     */
    protected function _construct()
    {
            parent::_construct();
            $this->setId('catalog_search_grid');
            $this->setDefaultSort('name');
            $this->setDefaultDir('ASC');
            $this->setSaveParametersInSession(true);
            $this->setUseAjax(true);
    }

    public function getQuery()
    {
        return Mage::registry('current_catalog_search');
    }

    /**
     * Prepare collection for Grid
     *
     * @return Enterprise_Search_Block_Adminhtml_Search_Grid
     */
    protected function _prepareCollection()
    {
        $this->setDefaultFilter(array('query_id_selected' => 1));

        $collection = Mage::getModel('Magento_CatalogSearch_Model_Query')
            ->getResourceCollection();

        $queryId = $this->getQuery()->getId();
        if ($queryId) {
            $collection->addFieldToFilter('query_id', array('nin' => $this->getQuery()->getId()));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for query selected flag
        if ( $column->getId() == 'query_id_selected' && $this->getQuery()->getId() ) {
            $selectedIds = $this->_getSelectedQueries();
            if (empty($selectedIds)) {
                $selectedIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('query_id', array('in'  => $selectedIds));
            }
            elseif(!empty($selectedIds)) {
                $this->getCollection()->addFieldToFilter('query_id', array('nin' => $selectedIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare Grid columns
     *
     * @return Enterprise_Search_Block_Adminhtml_Search_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('query_id_selected', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'query_id_selected',
            'values'    => $this->_getSelectedQueries(),
            'align'     => 'center',
            'index'     => 'query_id'
        ));

        $this->addColumn('query_id', array(
            'header'    => Mage::helper('Enterprise_Search_Helper_Data')->__('ID'),
            'width'     => '50px',
            'index'     => 'query_id',
        ));

        $this->addColumn('search_query', array(
            'header'    => Mage::helper('Enterprise_Search_Helper_Data')->__('Search Query'),
            'index'     => 'query_text',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('Enterprise_Search_Helper_Data')->__('Store'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_view'    => true,
                'sortable'      => false
            ));
        }

        $this->addColumn('num_results', array(
            'header'    => Mage::helper('Enterprise_Search_Helper_Data')->__('Results'),
            'index'     => 'num_results',
            'type'      => 'number'
        ));

        $this->addColumn('popularity', array(
            'header'    => Mage::helper('Enterprise_Search_Helper_Data')->__('Uses'),
            'index'     => 'popularity',
            'type'      => 'number'
        ));

        $this->addColumn('synonym_for', array(
            'header'    => Mage::helper('Enterprise_Search_Helper_Data')->__('Synonym'),
            'align'     => 'left',
            'index'     => 'synonym_for',
            'width'     => '160px'
        ));

        $this->addColumn('redirect', array(
            'header'    => Mage::helper('Enterprise_Search_Helper_Data')->__('Redirect URL'),
            'align'     => 'left',
            'index'     => 'redirect',
            'width'     => '200px'
        ));

        $this->addColumn('display_in_terms', array(
            'header'=>Mage::helper('Enterprise_Search_Helper_Data')->__('Suggested Term'),
            'sortable'=>true,
            'index'=>'display_in_terms',
            'type' => 'options',
            'width' => '100px',
            'options' => array(
                '1' => Mage::helper('Enterprise_Search_Helper_Data')->__('Yes'),
                '0' => Mage::helper('Enterprise_Search_Helper_Data')->__('No'),
            ),
            'align' => 'left',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('Enterprise_Search_Helper_Data')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(array(
                    'caption'   => Mage::helper('Enterprise_Search_Helper_Data')->__('Edit'),
                    'url'       => array(
                        'base'=>'*/*/edit'
                    ),
                    'field'   => 'id'
                )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'catalog',
        ));


        return parent::_prepareColumns();
    }

    /**
     * Retrieve Row Click callback URL
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * Retrieve selected related queries from grid
     *
     * @return array
     */
    public function _getSelectedQueries()
    {
        $queries = $this->getRequest()->getPost('selected_queries');

        $currentQueryId = $this->getQuery()->getId();
        $queryIds = array();
        if (is_null($queries) && !empty($currentQueryId)) {
            $queryIds = Mage::getResourceModel('Enterprise_Search_Model_Resource_Recommendations')
                ->getRelatedQueries($currentQueryId);
        }

        return $queryIds;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/relatedGrid', array('_current'=>true));
    }

    public function getQueriesJson()
    {
        $queries = array_flip($this->_getSelectedQueries());
        if (!empty($queries)) {
            return Mage::helper('Magento_Core_Helper_Data')->jsonEncode($queries);
        }
        return '{}';
    }
}
