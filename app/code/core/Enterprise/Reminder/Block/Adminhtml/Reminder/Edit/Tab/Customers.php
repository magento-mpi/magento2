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
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Matched rule customer grid block
 */
class Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Customers extends Enterprise_Enterprise_Block_Adminhtml_Widget_Grid
{
    /**
     * Intialize grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Customers
     */
    protected function _prepareCollection()
    {
        /* @var $collection Enterprise_Reminder_Model_Mysql4_Customer_Collection */
        $collection = Mage::getResourceModel('enterprise_reminder/customer_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for grid
     *
     * @return Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Customers
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'   => Mage::helper('enterprise_reminder')->__('ID'),
            'align'    => 'center',
            'width'    => 50,
            'index'    => 'entity_id',
            'renderer' => 'enterprise_reminder/adminhtml_widget_grid_column_renderer_id'
        ));

        $this->addColumn('email', array(
            'header'   => Mage::helper('enterprise_reminder')->__('Email'),
            'type'     => 'text',
            'align'    => 'left',
            'index'    => 'email',
            'renderer' => 'enterprise_reminder/adminhtml_widget_grid_column_renderer_email'
        ));

        $this->addColumn('associated_at', array(
            'header'   => Mage::helper('enterprise_reminder')->__('Matched At'),
            'align'    => 'left',
            'width'    => 150,
            'type'     => 'datetime',
            'default'  => '--',
            'index'    => 'associated_at'
        ));

        $this->addColumn('is_active', array(
            'header'   => Mage::helper('enterprise_reminder')->__('Thread Active'),
            'align'    => 'left',
            'type'     => 'options',
            'index'    => 'is_active',
            'options'  => array(
                '0' => Mage::helper('enterprise_reminder')->__('No'),
                '1' => Mage::helper('enterprise_reminder')->__('Yes')
            )
        ));

        $this->addColumn('code', array(
            'header'   => Mage::helper('enterprise_reminder')->__('Coupon'),
            'align'    => 'left',
            'default'  => Mage::helper('enterprise_reminder')->__('N/A'),
            'index'    => 'code'
        ));

        $this->addColumn('usage_limit', array(
            'header'   => Mage::helper('enterprise_reminder')->__('Coupon Usage Limit'),
            'align'    => 'left',
            'default'  => '0',
            'index'    => 'usage_limit'
        ));

        $this->addColumn('usage_per_customer', array(
            'header'   => Mage::helper('enterprise_reminder')->__('Coupon Usage per Customer'),
            'align'    => 'left',
            'default'  => '0',
            'index'    => 'usage_per_customer'
        ));

        $this->addColumn('emails_sent', array(
            'header'   => Mage::helper('enterprise_reminder')->__('Emails Sent'),
            'align'    => 'left',
            'default'  => '0',
            'index'    => 'emails_sent'
        ));

        $this->addColumn('emails_failed', array(
            'header'   => Mage::helper('enterprise_reminder')->__('Emails Failed'),
            'align'    => 'left',
            'index'    => 'emails_failed'
        ));

        $this->addColumn('last_sent', array(
            'header'   => Mage::helper('enterprise_reminder')->__('Last Sent At'),
            'align'    => 'left',
            'width'    => 150,
            'type'     => 'datetime',
            'default'  => '--',
            'index'    => 'last_sent'
        ));

        return parent::_prepareColumns();
    }
}
