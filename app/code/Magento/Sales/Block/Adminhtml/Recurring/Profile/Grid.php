<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profiles grid
 */
namespace Magento\Sales\Block\Adminhtml\Recurring\Profile;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Set ajax/session parameters
     */
    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData = null;

    /**
     * @var \Magento\Sales\Model\Resource\Recurring\Profile\CollectionFactory
     */
    protected $_profileCollection;

    /**
     * @var \Magento\Sales\Model\Recurring\ProfileFactory
     */
    protected $_recurringProfileFactory;

    /**
     * @var \Magento\RecurringProfile\Block\Fields
     */
    protected $_fields;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Sales\Model\Resource\Recurring\Profile\CollectionFactory $profileCollection
     * @param \Magento\Sales\Model\Recurring\ProfileFactory $recurringProfileFactory
     * @param \Magento\RecurringProfile\Block\Fields $fields
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Sales\Model\Resource\Recurring\Profile\CollectionFactory $profileCollection,
        \Magento\Sales\Model\Recurring\ProfileFactory $recurringProfileFactory,
        \Magento\RecurringProfile\Block\Fields $fields,
        array $data = array()
    ) {
        $this->_paymentData = $paymentData;
        $this->_profileCollection = $profileCollection;
        $this->_recurringProfileFactory = $recurringProfileFactory;
        parent::__construct($context, $backendHelper, $data);
        $this->_fields = $fields;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_recurring_profile_grid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return \Magento\Backend\Block\Widget\Grid
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
     * @return \Magento\Sales\Block\Adminhtml\Recurring\Profile\Grid
     */
    protected function _prepareColumns()
    {
        $profile = $this->_recurringProfileFactory->create();

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
            'options' => $profile->getAllStates(),
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

        $methods = array();
        foreach ($this->_paymentData->getRecurringProfileMethods() as $method) {
            $methods[$method->getCode()] = $method->getTitle();
        }
        $this->addColumn('method_code', array(
            'header'  => $this->_fields->getFieldLabel('method_code'),
            'index'   => 'method_code',
            'type'    => 'options',
            'options' => $methods,
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
     * @param \Magento\Object
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('sales/recurring_profile/view', array('profile' => $row->getId()));
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
