<?php
/**
 * description
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  Promo_Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_search_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalogsearch/search')
            ->getResourceCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('search_id', array(
            'header'    => __('ID'),
            'width'     => '50px',
            'index'     => 'search_id',
        ));

        $this->addColumn('search_query', array(
            'header'    => __('Search Query'),
            'index'     => 'search_query',
        ));

        $this->addColumn('num_results', array(
            'header'    => __('Results'),
            'index'     => 'num_results',
        ));

        $this->addColumn('popularity', array(
            'header'    => __('Number of Uses'),
            'index'     => 'popularity',
        ));

        $this->addColumn('synonim_for', array(
            'header'    => __('Synonim for'),
            'align'     => 'left',
            'index'     => 'synonim_for',
        ));
        
        $this->addColumn('redirect', array(
            'header'    => __('Redirect'),
            'align'     => 'left',
            'index'     => 'redirect',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('id' => $row->getSearchId()));
    }
}