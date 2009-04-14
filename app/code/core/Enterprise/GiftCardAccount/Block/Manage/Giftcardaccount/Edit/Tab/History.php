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
 * @category   Enterprise
 * @package    Enterprise_GiftCardAccount
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


class Enterprise_GiftCardAccount_Block_Manage_Giftcardaccount_Edit_Tab_History extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setId('historyGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('updated_at');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('enterprise_giftcardaccount/history')
            ->getCollection()
            ->addFieldToFilter('giftcardaccount_id', Mage::registry('current_giftcardaccount')->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('enterprise_giftcardaccount')->__('Date'),
            'index'     => 'updated_at',
            'type'      => 'datetime',
            'filter'    => false,
            'width'     => 100,
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('enterprise_giftcardaccount')->__('Action'),
            'width'     => 100,
            'index'     => 'action',
            'sortable'  => false,
            'type'      => 'options',
            'options'   => Mage::getSingleton('enterprise_giftcardaccount/history')->getActionNamesArray(),
        ));

        $this->addColumn('balance_delta', array(
            'header'    => Mage::helper('enterprise_giftcardaccount')->__('Balance Change'),
            'width'     => 50,
            'index'     => 'balance_delta',
            'type'      => 'price',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'enterprise_giftcardaccount/manage_widget_grid_column_renderer_currency',
        ));

        $this->addColumn('balance_amount', array(
            'header'    => Mage::helper('enterprise_giftcardaccount')->__('Balance'),
            'width'     => 50,
            'index'     => 'balance_amount',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'enterprise_giftcardaccount/manage_widget_grid_column_renderer_currency',
        ));

        $this->addColumn('additional_info', array(
            'header'    => Mage::helper('enterprise_giftcardaccount')->__('Additional information'),
            'index'     => 'additional_info',
            'sortable'  => false,
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/gridHistory', array('_current'=> true));
    }
}
