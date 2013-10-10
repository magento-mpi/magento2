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
namespace Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab;

class Customers extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * Customer Resource Collection
     *
     * @var \Magento\Reminder\Model\Resource\Customer\Collection
     */
    protected $_customerCollection;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Reminder\Model\Resource\Customer\Collection $customerCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Reminder\Model\Resource\Customer\Collection $customerCollection,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
        $this->_customerCollection = $customerCollection;
    }

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
     * @return \Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\Customers
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_customerCollection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for grid
     *
     * @return \Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\Customers
     */
    protected function _prepareColumns()
    {
        $this->addColumn('grid_entity_id', array(
            'header'   => __('ID'),
            'align'    => 'center',
            'width'    => 50,
            'index'    => 'entity_id',
            'renderer' => 'Magento\Reminder\Block\Adminhtml\Widget\Grid\Column\Renderer\Id'
        ));

        $this->addColumn('grid_email', array(
            'header'   => __('Email'),
            'type'     => 'text',
            'align'    => 'left',
            'index'    => 'email',
            'renderer' => 'Magento\Reminder\Block\Adminhtml\Widget\Grid\Column\Renderer\Email'
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
