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
 * Adminhtml billing agreements grid
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Billing\Agreement;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData = null;

    /**
     * @var \Magento\Sales\Model\Resource\Billing\Agreement\CollectionFactory
     */
    protected $_agreementFactory;

    /**
     * @var \Magento\Sales\Model\Billing\Agreement
     */
    protected $_agreementModel;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Sales\Model\Resource\Billing\Agreement\CollectionFactory $agreementFactory
     * @param \Magento\Sales\Model\Billing\Agreement $agreementModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Sales\Model\Resource\Billing\Agreement\CollectionFactory $agreementFactory,
        \Magento\Sales\Model\Billing\Agreement $agreementModel,
        array $data = array()
    ) {
        $this->_paymentData = $paymentData;
        $this->_agreementFactory = $agreementFactory;
        $this->_agreementModel = $agreementModel;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('billing_agreements');
        $this->setUseAjax(true);
        $this->setDefaultSort('agreement_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('sales/billing_agreement/grid', array('_current' => true));
    }

    /**
     * Retrieve row url
     *
     * @param object $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('sales/billing_agreement/view', array('agreement' => $item->getAgreementId()));
    }

    /**
     * Prepare collection for grid
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        /** @var \Magento\Sales\Model\Resource\Billing\Agreement\Collection $collection */
        $collection = $this->_agreementFactory->create()
            ->addCustomerDetails();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('agreement_id', array(
            'header'            => __('ID'),
            'index'             => 'agreement_id',
            'type'              => 'text',
            'header_css_class'  => 'col-id',
            'column_css_class'  => 'col-id'
        ));

        $this->addColumn('customer_email', array(
            'header'            => __('Email'),
            'index'             => 'customer_email',
            'type'              => 'text',
            'header_css_class'  => 'col-mail',
            'column_css_class'  => 'col-mail'
        ));

        $this->addColumn('customer_firstname', array(
            'header'            => __('First Name'),
            'index'             => 'customer_firstname',
            'type'              => 'text',
            'escape'            => true,
            'header_css_class'  => 'col-name',
            'column_css_class'  => 'col-name'
        ));

        $this->addColumn('customer_lastname', array(
            'header'            => __('Last Name'),
            'index'             => 'customer_lastname',
            'type'              => 'text',
            'escape'            => true,
            'header_css_class'  => 'col-last-name',
            'column_css_class'  => 'col-last-name'
        ));

        $this->addColumn('method_code', array(
            'header'            => __('Payment Method'),
            'index'             => 'method_code',
            'type'              => 'options',
            'options'           => $this->_paymentData->getAllBillingAgreementMethods(),
            'header_css_class'  => 'col-payment',
            'column_css_class'  => 'col-payment'
        ));

        $this->addColumn('reference_id', array(
            'header'            => __('Reference ID'),
            'index'             => 'reference_id',
            'type'              => 'text',
            'header_css_class'  => 'col-reference',
            'column_css_class'  => 'col-reference'
        ));

        $this->addColumn('status', array(
            'header'            => __('Status'),
            'index'             => 'status',
            'type'              => 'options',
            'options'           => $this->_agreementModel->getStatusesArray(),
            'header_css_class'  => 'col-status',
            'column_css_class'  => 'col-status'
        ));

        $this->addColumn('created_at', array(
            'header'            => __('Created'),
            'index'             => 'agreement_created_at',
            'type'              => 'datetime',
            'align'             => 'center',
            'default'           => __('N/A'),
            'html_decorators'   => array('nobr'),
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('updated_at', array(
            'header'            => __('Updated'),
            'index'             => 'agreement_updated_at',
            'type'              => 'datetime',
            'align'             => 'center',
            'default'           => __('N/A'),
            'html_decorators'   => array('nobr'),
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        return parent::_prepareColumns();
    }
}
