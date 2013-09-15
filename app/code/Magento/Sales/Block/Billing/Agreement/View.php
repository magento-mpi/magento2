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
 * Customer account billing agreement view block
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Billing\Agreement;

class View extends \Magento\Core\Block\Template
{
    /**
     * Payment methods array
     *
     * @var array
     */
    protected $_paymentMethods = array();

    /**
     * Billing Agreement instance
     *
     * @var \Magento\Sales\Model\Billing\Agreement
     */
    protected $_billingAgreementInstance = null;

    /**
     * Related orders collection
     *
     * @var \Magento\Sales\Model\Resource\Order\Collection
     */
    protected $_relatedOrders = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve related orders collection
     *
     * @return \Magento\Sales\Model\Resource\Order\Collection
     */
    public function getRelatedOrders()
    {
        if (is_null($this->_relatedOrders)) {
            $this->_relatedOrders = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getId())
                ->addFieldToFilter(
                    'state',
                    array('in' => \Mage::getSingleton('Magento\Sales\Model\Order\Config')->getVisibleOnFrontStates())
                )
                ->addBillingAgreementsFilter($this->_billingAgreementInstance->getAgreementId())
                ->setOrder('created_at', 'desc');
        }
        return $this->_relatedOrders;
    }

    /**
     * Retrieve order item value by key
     *
     * @param \Magento\Sales\Model\Order $order
     * @param string $key
     * @return string
     */
    public function getOrderItemValue(\Magento\Sales\Model\Order $order, $key)
    {
        $escape = true;
        switch ($key) {
            case 'order_increment_id':
                $value = $order->getIncrementId();
                break;
            case 'created_at':
                $value = $this->helper('Magento\Core\Helper\Data')->formatDate($order->getCreatedAt(), 'short', true);
                break;
            case 'shipping_address':
                $value = $order->getShippingAddress()
                    ? $this->escapeHtml($order->getShippingAddress()->getName()) : __('N/A');
                break;
            case 'order_total':
                $value = $order->formatPrice($order->getGrandTotal());
                $escape = false;
                break;
            case 'status_label':
                $value = $order->getStatusLabel();
                break;
            case 'view_url':
                $value = $this->getUrl('*/order/view', array('order_id' => $order->getId()));
                break;
            default:
                $value = ($order->getData($key)) ? $order->getData($key) : __('N/A');
        }
        return ($escape) ? $this->escapeHtml($value) : $value;
    }

    /**
     * Set pager
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if (is_null($this->_billingAgreementInstance)) {
            $this->_billingAgreementInstance = $this->_coreRegistry->registry('current_billing_agreement');
        }
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('Magento\Page\Block\Html\Pager')
            ->setCollection($this->getRelatedOrders())->setIsOutputRequired(false);
        $this->setChild('pager', $pager);
        $this->getRelatedOrders()->load();

        return $this;
    }

    /**
     * Load available billing agreement methods
     *
     * @return array
     */
    protected function _loadPaymentMethods()
    {
        if (!$this->_paymentMethods) {
            foreach ($this->helper('Magento\Payment\Helper\Data')->getBillingAgreementMethods() as $paymentMethod) {
                $this->_paymentMethods[$paymentMethod->getCode()] = $paymentMethod->getTitle();
            }
        }
        return $this->_paymentMethods;
    }

    /**
     * Set data to block
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_loadPaymentMethods();
        $this->setBackUrl($this->getUrl('*/billing_agreement/'));
        if ($this->_billingAgreementInstance) {
            $this->setReferenceId($this->_billingAgreementInstance->getReferenceId());

            $this->setCanCancel($this->_billingAgreementInstance->canCancel());
            $this->setCancelUrl(
                $this->getUrl('*/billing_agreement/cancel', array(
                    '_current' => true,
                    'payment_method' => $this->_billingAgreementInstance->getMethodCode()))
            );

            $paymentMethodTitle = $this->_billingAgreementInstance->getAgreementLabel();
            $this->setPaymentMethodTitle($paymentMethodTitle);

            $createdAt = $this->_billingAgreementInstance->getCreatedAt();
            $updatedAt = $this->_billingAgreementInstance->getUpdatedAt();
            $this->setAgreementCreatedAt(
                ($createdAt) ? $this->helper('Magento\Core\Helper\Data')->formatDate($createdAt, 'short', true) : __('N/A')
            );
            if ($updatedAt) {
                $this->setAgreementUpdatedAt(
                    $this->helper('Magento\Core\Helper\Data')->formatDate($updatedAt, 'short', true)
                );
            }
            $this->setAgreementStatus($this->_billingAgreementInstance->getStatusLabel());
        }

        return parent::_toHtml();
    }
}
