<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Search query relations edit grid
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Block_Adminhtml_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     *
     */
    public function __construct()
    {
            parent::__construct();
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
     * @return Mage_Adminhtml_Block_Catalog_Search_Grid
     */
    protected function _prepareCollection()
    {
        $this->setDefaultFilter(array('query_id_selected' => 1));

        $collection = Mage::getModel('catalogsearch/query')
            ->getResourceCollection();
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
     * @return Mage_Adminhtml_Block_Catalog_Search_Grid
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
            'header'    => Mage::helper('catalog')->__('ID'),
            'width'     => '50px',
            'index'     => 'query_id',
        ));

        $this->addColumn('search_query', array(
            'header'    => Mage::helper('catalog')->__('Search Query'),
            'index'     => 'query_text',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('catalog')->__('Store'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_view'    => true,
                'sortable'      => false
            ));
        }

        $this->addColumn('num_results', array(
            'header'    => Mage::helper('catalog')->__('Results'),
            'index'     => 'num_results',
            'type'      => 'number'
        ));

        $this->addColumn('popularity', array(
            'header'    => Mage::helper('catalog')->__('Number of Uses'),
            'index'     => 'popularity',
            'type'      => 'number'
        ));

        $this->addColumn('synonym_for', array(
            'header'    => Mage::helper('catalog')->__('Synonym For'),
            'align'     => 'left',
            'index'     => 'synonym_for',
            'width'     => '160px'
        ));

        $this->addColumn('redirect', array(
            'header'    => Mage::helper('catalog')->__('Redirect'),
            'align'     => 'left',
            'index'     => 'redirect',
            'width'     => '200px'
        ));

        $this->addColumn('display_in_terms', array(
            'header'=>Mage::helper('catalog')->__('Display in Suggested Terms'),
            'sortable'=>true,
            'index'=>'display_in_terms',
            'type' => 'options',
            'width' => '100px',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'left',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(array(
                    'caption'   => Mage::helper('catalog')->__('Edit'),
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
            $queryIds = Mage::getResourceModel('enterprise_search/recommendations')->getRelatedQueries($currentQueryId);
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
            return Mage::helper('core')->jsonEncode($queries);
        }
        return '{}';
    }
}
