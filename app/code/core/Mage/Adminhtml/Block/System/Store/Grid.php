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
 * Adminhtml store grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_Adminhtml_Block_System_Store_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('storeGrid');
        $this->setDefaultSort('name');
        $this->setDefaultSort('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('core/store')
            ->getCollection()
            ->joinGroupsAndWebsites()
            ->load();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
//        $this->addColumn('website_id', array(
//            'header'    => Mage::helper('core')->__('Website ID'),
//            'align'     =>'right',
//            'width'     => '50px',
//            'index'     => 'website_id',
//            'renderer'  => 'adminhtml/system_store_grid_render_website'
//        ));

        $this->addColumn('website_title', array(
            'header'    => Mage::helper('core')->__('Website name'),
            'align'     =>'left',
            'index'     => 'website_title',
            'renderer'  => 'adminhtml/system_store_grid_render_website'
        ));

//        $this->addColumn('group_id', array(
//            'header'    => Mage::helper('core')->__('Store group ID'),
//            'align'     =>'right',
//            'width'     => '50px',
//            'index'     => 'group_id',
//            'renderer'  => 'adminhtml/system_store_grid_render_group'
//        ));

        $this->addColumn('group_title', array(
            'header'    => Mage::helper('core')->__('Store Group name'),
            'align'     =>'left',
            'index'     => 'group_title',
            'renderer'  => 'adminhtml/system_store_grid_render_group'
        ));

//        $this->addColumn('store_id', array(
//            'header'    => Mage::helper('core')->__('Store ID'),
//            'align'     =>'right',
//            'width'     => '50px',
//            'index'     => 'store_id',
//            'renderer'  => 'adminhtml/system_store_grid_render_store'
//        ));

        $this->addColumn('store_title', array(
            'header'    => Mage::helper('core')->__('Store name'),
            'align'     =>'left',
            'index'     => 'name',
            'renderer'  => 'adminhtml/system_store_grid_render_store'
        ));

        return parent::_prepareColumns();

    }
}