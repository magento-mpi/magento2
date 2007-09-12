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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * description
 *
 * @category    Mage
 * @category   Mage
 * @package    Mage_Adminhtml
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
            'header'    => __('Synonym for'),
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