<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Adminhtml\Customer;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * Set default sort
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('registry_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return \Magento\GiftRegistry\Block\Adminhtml\Giftregistry_Customer_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Magento\GiftRegistry\Model\Resource\Entity\Collection */
        $collection = \Mage::getModel('Magento\GiftRegistry\Model\Entity')->getCollection();
        $collection->filterByCustomerId($this->getRequest()->getParam('id'));
        $collection->addRegistryInfo();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for grid
     *
     * @return \Magento\GiftRegistry\Block\Adminhtml\Giftregistry_Customer_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('title', array(
            'header' => __('Event'),
            'index'  => 'title'
        ));

        $this->addColumn('registrants', array(
            'header' => __('Recipients'),
            'index'  => 'registrants'
        ));

        $this->addColumn('event_date', array(
            'header'  => __('Event Date'),
            'index'   => 'event_date',
            'type'    => 'date',
            'default' => '--'
        ));

        $this->addColumn('qty', array(
            'header' => __('Total Items'),
            'index'  => 'qty',
            'type'   => 'number'
        ));

        $this->addColumn('qty_fulfilled', array(
            'header' => __('Fulfilled'),
            'index'  => 'qty_fulfilled',
            'type'   => 'number',
        ));

        $this->addColumn('qty_remaining', array(
            'header' => __('Remaining'),
            'index'  => 'qty_remaining',
            'type'   => 'number'
        ));

        $this->addColumn('is_public', array(
            'header'  => __('Public'),
            'index'   => 'is_public',
            'type'    => 'options',
            'options' => array(
                '0' => __('No'),
                '1' => __('Yes'),
            )
        ));

        if (!\Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header' => __('Website'),
                'index'  => 'website_id',
                'type'   => 'options',
                'options' => \Mage::getSingleton('Magento\Core\Model\System\Store')->getWebsiteOptionHash()
            ));
        }

        return parent::_prepareColumns();
    }

    /**
     * Retrieve row url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id'       => $row->getId(),
            'customer' => $row->getCustomerId()
        ));
    }

    /**
     * Retrieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
