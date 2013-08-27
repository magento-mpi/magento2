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
class Magento_Sales_Block_Adminhtml_Billing_Agreement_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Payment data
     *
     * @var Magento_Payment_Helper_Data
     */
    protected $_paymentData = null;

    /**
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Payment_Helper_Data $paymentData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_paymentData = $paymentData;
        parent::__construct($context, $storeManager, $urlModel, $data);
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
        return $this->getUrl('*/sales_billing_agreement/grid', array('_current' => true));
    }

    /**
     * Retrieve row url
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('*/sales_billing_agreement/view', array('agreement' => $item->getAgreementId()));
    }

    /**
     * Prepare collection for grid
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Magento_Sales_Model_Resource_Billing_Agreement_Collection')
            ->addCustomerDetails();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
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
            'options'           => Mage::getSingleton('Magento_Sales_Model_Billing_Agreement')->getStatusesArray(),
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
