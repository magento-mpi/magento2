<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_History extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_collection;

    protected function _construct()
    {
        parent::_construct();
        $this->setId('historyGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Enterprise_GiftCardAccount_Model_History')
            ->getCollection()
            ->addFieldToFilter('giftcardaccount_id', Mage::registry('current_giftcardaccount')->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('ID'),
            'index'     => 'history_id',
            'type'      => 'int',
            'width'     => 50,
        ));

        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Date'),
            'index'     => 'updated_at',
            'type'      => 'datetime',
            'filter'    => false,
            'width'     => 100,
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Action'),
            'width'     => 100,
            'index'     => 'action',
            'sortable'  => false,
            'type'      => 'options',
            'options'   => Mage::getSingleton('Enterprise_GiftCardAccount_Model_History')->getActionNamesArray(),
        ));

        $currency = Mage::app()->getWebsite(Mage::registry('current_giftcardaccount')->getWebsiteId())->getBaseCurrencyCode();
        $this->addColumn('balance_delta', array(
            'header'        => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Balance Change'),
            'width'         => 50,
            'index'         => 'balance_delta',
            'sortable'      => false,
            'filter'        => false,
            'type'          => 'price',
            'currency_code' => $currency,
        ));

        $this->addColumn('balance_amount', array(
            'header'        => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Balance'),
            'width'         => 50,
            'index'         => 'balance_amount',
            'sortable'      => false,
            'filter'        => false,
            'type'          => 'price',
            'currency_code' => $currency,
        ));

        $this->addColumn('additional_info', array(
            'header'    => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('More Information'),
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
