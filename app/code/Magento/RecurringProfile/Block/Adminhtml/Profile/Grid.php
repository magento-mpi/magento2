<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Block\Adminhtml\Profile;

/**
 * Class Grid - Recurring profiles grid
 * @todo: convert to layout update
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\RecurringProfile\Model\Resource\Profile\CollectionFactory
     */
    protected $_profileCollection;

    /**
     * @var \Magento\RecurringProfile\Model\States
     */
    protected $recurringStates;

    /**
     * @var \Magento\RecurringProfile\Block\Fields
     */
    protected $_fields;

    /**
     * @var \Magento\RecurringProfile\Model\Method\PaymentMethodsList
     */
    protected $payments;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\RecurringProfile\Model\Resource\Profile\CollectionFactory $profileCollection
     * @param \Magento\RecurringProfile\Model\States $recurringStates
     * @param \Magento\RecurringProfile\Block\Fields $fields
     * @param \Magento\RecurringProfile\Model\Method\PaymentMethodsList $payments
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\RecurringProfile\Model\Resource\Profile\CollectionFactory $profileCollection,
        \Magento\RecurringProfile\Model\States $recurringStates,
        \Magento\RecurringProfile\Block\Fields $fields,
        \Magento\RecurringProfile\Model\Method\PaymentMethodsList $payments,
        array $data = array()
    ) {
        $this->_profileCollection = $profileCollection;
        $this->recurringStates = $recurringStates;
        $this->payments = $payments;
        parent::__construct($context, $backendHelper, $data);
        $this->_fields = $fields;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('recurring_profile_grid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_profileCollection->create();
        $this->setCollection($collection);
        if (!$this->getParam($this->getVarNameSort())) {
            $collection->setOrder('profile_id', 'desc');
        }
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('reference_id', array(
            'header' => $this->_fields->getFieldLabel('reference_id'),
            'index' => 'reference_id',
            'html_decorators' => array('nobr'),
            'width' => 1,
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'     => __('Store'),
                'index'      => 'store_id',
                'type'       => 'store',
                'store_view' => true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('state', array(
            'header' => $this->_fields->getFieldLabel('state'),
            'index' => 'state',
            'type'  => 'options',
            'options' => $this->recurringStates->toOptionArray(),
            'html_decorators' => array('nobr'),
            'width' => 1,
        ));

        $this->addColumn('created_at', array(
            'header' => $this->_fields->getFieldLabel('created_at'),
            'index' => 'created_at',
            'type' => 'datetime',
            'html_decorators' => array('nobr'),
            'width' => 1,
        ));

        $this->addColumn('updated_at', array(
            'header' => $this->_fields->getFieldLabel('updated_at'),
            'index' => 'updated_at',
            'type' => 'datetime',
            'html_decorators' => array('nobr'),
            'width' => 1,
        ));

        $this->addColumn('method_code', array(
            'header'  => $this->_fields->getFieldLabel('method_code'),
            'index'   => 'method_code',
            'type'    => 'options',
            'options' => $this->payments->toOptionArray(),
        ));

        $this->addColumn('schedule_description', array(
            'header' => $this->_fields->getFieldLabel('schedule_description'),
            'index' => 'schedule_description',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return row url for js event handlers
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('sales/recurringProfile/view', array('profile' => $row->getId()));
    }

    /**
     * Return grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('sales/*/grid', array('_current'=>true));
    }
}
