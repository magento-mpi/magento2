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
 * @category   Mage
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Google Base Item Types Mapping grid
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Block_Adminhtml_Types_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('types_grid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('googlebase/type_collection')->addItemsCount();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('attribute_set_name',
            array(
                'header'=> $this->__('Attributes Set'),
                'width' => '150px',
                'index'     => 'attribute_set_name',
        ));
        $this->addColumn('gbase_itemtype',
            array(
                'header'=> $this->__('Google Base Item type'),
                'width' => '150px',
                'index'     => 'gbase_itemtype',
        ));

        $this->addColumn('items_total',
            array(
                'header'=> Mage::helper('catalog')->__('Total Qty Base Items'),
                'width' => '150px',
                'index'     => 'items_total',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}