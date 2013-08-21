<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Matched rule customer grid block
 */
class Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Customers extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Intialize grid
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Customers
     */
    protected function _prepareCollection()
    {
        /* @var $collection Magento_Reminder_Model_Resource_Customer_Collection */
        $collection = Mage::getResourceModel('Magento_Reminder_Model_Resource_Customer_Collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for grid
     *
     * @return Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Customers
     */
    protected function _prepareColumns()
    {
        $this->addColumn('grid_entity_id', array(
            'header'   => __('ID'),
            'align'    => 'center',
            'width'    => 50,
            'index'    => 'entity_id',
            'renderer' => 'Magento_Reminder_Block_Adminhtml_Widget_Grid_Column_Renderer_Id'
        ));

        $this->addColumn('grid_email', array(
            'header'   => __('Email'),
            'type'     => 'text',
            'align'    => 'left',
            'index'    => 'email',
            'renderer' => 'Magento_Reminder_Block_Adminhtml_Widget_Grid_Column_Renderer_Email'
        ));

        $this->addColumn('grid_associated_at', array(
            'header'   => __('Matched At'),
            'align'    => 'left',
            'width'    => 150,
            'type'     => 'datetime',
            'default'  => '--',
            'index'    => 'associated_at'
        ));

        $this->addColumn('grid_is_active', array(
            'header'   => __('Thread Active'),
            'align'    => 'left',
            'type'     => 'options',
            'index'    => 'is_active',
            'options'  => array(
                '0' => __('No'),
                '1' => __('Yes')
            )
        ));

        $this->addColumn('grid_code', array(
            'header'   => __('Coupon'),
            'align'    => 'left',
            'default'  => __('N/A'),
            'index'    => 'code'
        ));

        $this->addColumn('grid_usage_limit', array(
            'header'   => __('Coupon Use Limit'),
            'align'    => 'left',
            'default'  => '0',
            'index'    => 'usage_limit'
        ));

        $this->addColumn('grid_usage_per_customer', array(
            'header'   => __('Coupon Use Per Customer'),
            'align'    => 'left',
            'default'  => '0',
            'index'    => 'usage_per_customer'
        ));

        $this->addColumn('grid_emails_sent', array(
            'header'   => __('Emails Sent'),
            'align'    => 'left',
            'default'  => '0',
            'index'    => 'emails_sent'
        ));

        $this->addColumn('grid_emails_failed', array(
            'header'   => __('Emails Failed'),
            'align'    => 'left',
            'index'    => 'emails_failed'
        ));

        $this->addColumn('grid_last_sent', array(
            'header'   => __('Last Sent'),
            'align'    => 'left',
            'width'    => 150,
            'type'     => 'datetime',
            'default'  => '--',
            'index'    => 'last_sent'
        ));

        return parent::_prepareColumns();
    }
}
