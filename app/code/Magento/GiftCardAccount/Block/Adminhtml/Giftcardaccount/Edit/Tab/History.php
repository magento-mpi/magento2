<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab;

class History extends \Magento\Adminhtml\Block\Widget\Grid
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
        $collection = \Mage::getModel('\Magento\GiftCardAccount\Model\History')
            ->getCollection()
            ->addFieldToFilter('giftcardaccount_id', \Mage::registry('current_giftcardaccount')->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => __('ID'),
            'index'     => 'history_id',
            'type'      => 'int',
            'width'     => 50,
        ));

        $this->addColumn('updated_at', array(
            'header'    => __('Date'),
            'index'     => 'updated_at',
            'type'      => 'datetime',
            'filter'    => false,
            'width'     => 100,
        ));

        $this->addColumn('action', array(
            'header'    => __('Action'),
            'width'     => 100,
            'index'     => 'action',
            'sortable'  => false,
            'type'      => 'options',
            'options'   => \Mage::getSingleton('Magento\GiftCardAccount\Model\History')->getActionNamesArray(),
        ));

        $currency = \Mage::app()->getWebsite(\Mage::registry('current_giftcardaccount')->getWebsiteId())->getBaseCurrencyCode();
        $this->addColumn('balance_delta', array(
            'header'        => __('Balance Change'),
            'width'         => 50,
            'index'         => 'balance_delta',
            'sortable'      => false,
            'filter'        => false,
            'type'          => 'price',
            'currency_code' => $currency,
        ));

        $this->addColumn('balance_amount', array(
            'header'        => __('Balance'),
            'width'         => 50,
            'index'         => 'balance_amount',
            'sortable'      => false,
            'filter'        => false,
            'type'          => 'price',
            'currency_code' => $currency,
        ));

        $this->addColumn('additional_info', array(
            'header'    => __('More Information'),
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
