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

class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcardaccountGrid');
        $this->setDefaultSort('giftcardaccount_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('giftcardaccount_filter');

    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getResourceModel('enterprise_giftcardaccount/giftcardaccount_collection');

        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('giftcardaccount_id',
            array(
                'header'=> Mage::helper('enterprise_giftcardaccount')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'giftcardaccount_id',
        ));

        $this->addColumn('code',
            array(
                'header'=> Mage::helper('enterprise_giftcardaccount')->__('Code'),
                'index' => 'code',
        ));

        $websites = Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForGridFilter(true, true);

        $this->addColumn('website',
            array(
                'header'    => Mage::helper('enterprise_giftcardaccount')->__('Website'),
                'width'     => '100px',
                'index'     => 'website_id',
                'type'      => 'options',
                'options'   => $websites,
        ));

        $this->addColumn('date_created',
            array(
                'header'=> Mage::helper('enterprise_giftcardaccount')->__('Date Created'),
                'width' => '120px',
                'type'  => 'date',
                'index' => 'date_created',
        ));

        $this->addColumn('date_expires',
            array(
                'header'  => Mage::helper('enterprise_giftcardaccount')->__('Expiration Date'),
                'width'   => '120px',
                'type'    => 'date',
                'index'   => 'date_expires',
                'default' => '--',
        ));

        $this->addColumn('status',
            array(
                'header'    => Mage::helper('enterprise_giftcardaccount')->__('Active'),
                'width'     => '50px',
                'align'     => 'center',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => array(
                    Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED =>
                        Mage::helper('enterprise_giftcardaccount')->__('Yes'),
                    Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED =>
                        Mage::helper('enterprise_giftcardaccount')->__('No'),
                ),
        ));

        $this->addColumn('state',
            array(
                'header'    => Mage::helper('enterprise_giftcardaccount')->__('Status'),
                'width'     => '100px',
                'align'     => 'center',
                'index'     => 'state',
                'type'      => 'options',
                'options'   => Mage::getModel('enterprise_giftcardaccount/giftcardaccount')->getStatesAsOptionList(),
        ));

        $this->addColumn('balance',
            array(
                'header'        => Mage::helper('enterprise_giftcardaccount')->__('Balance'),
                'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
                'type'          => 'price',
                'index'         => 'balance',
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('enterprise_giftcardaccount')->__('Action'),
            'align'     => 'center',
            'width'     => '10px',
            'format'    => '<a href="'.$this->getUrl('*/*/edit/id/$giftcardaccount_id').'">'.Mage::helper('enterprise_giftcardaccount')->__('Edit').'</a>',
            'filter'    => false,
            'sortable'  => false,
            'is_system' => true
        ));


        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}