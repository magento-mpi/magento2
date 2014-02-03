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
 * Customer account billing agreements block
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Billing;

class Agreements extends \Magento\View\Element\Template
{
    /**
     * Payment methods array
     *
     * @var array
     */
    protected $_paymentMethods = array();

    /**
     * Billing agreements collection
     *
     * @var \Magento\Sales\Model\Resource\Billing\Agreement\Collection
     */
    protected $_billingAgreements = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\Resource\Billing\Agreement\CollectionFactory
     */
    protected $_agreementCollection;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Resource\Billing\Agreement\CollectionFactory $agreementCollection
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Resource\Billing\Agreement\CollectionFactory $agreementCollection,
        \Magento\Payment\Helper\Data $paymentHelper,
        array $data = array()
    ) {
        $this->_paymentHelper = $paymentHelper;
        $this->_customerSession = $customerSession;
        $this->_agreementCollection = $agreementCollection;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Set Billing Agreement instance
     *
     * @return \Magento\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager')
            ->setCollection($this->getBillingAgreements())->setIsOutputRequired(false);
        $this->setChild('pager', $pager)
            ->setBackUrl($this->getUrl('customer/account/'));
        $this->getBillingAgreements()->load();
        return $this;
    }

    /**
     * Retrieve billing agreements collection
     *
     * @return \Magento\Sales\Model\Resource\Billing\Agreement\Collection
     */
    public function getBillingAgreements()
    {
        if (is_null($this->_billingAgreements)) {
            $this->_billingAgreements = $this->_agreementCollection->create()
                ->addFieldToFilter('customer_id', $this->_customerSession->getCustomerId())
                ->setOrder('agreement_id', 'desc');
        }
        return $this->_billingAgreements;
    }

    /**
     * Retrieve item value by key
     *
     * @param \Magento\Object|\Magento\Sales\Model\Billing\Agreement $item
     * @param string $key
     * @return mixed
     */
    public function getItemValue(\Magento\Sales\Model\Billing\Agreement $item, $key)
    {
        switch ($key) {
            case 'created_at':
            case 'updated_at':
                $value = ($item->getData($key))
                    ? $this->formatDate($item->getData($key), 'short', true)
                    : __('N/A');
                break;
            case 'edit_url':
                $value = $this->getUrl('*/billing_agreement/view', array('agreement' => $item->getAgreementId()));
                break;
            case 'payment_method_label':
                $label = $item->getAgreementLabel();
                $value = ($label) ? $label : __('N/A');
                break;
            case 'status':
                $value = $item->getStatusLabel();
                break;
            default:
                $value = ($item->getData($key)) ? $item->getData($key) : __('N/A');
                break;
        }
        return $this->escapeHtml($value);
    }

    /**
     * Load available billing agreement methods
     *
     * @return array
     */
    protected function _loadPaymentMethods()
    {
        if (!$this->_paymentMethods) {
            foreach ($this->_paymentHelper->getBillingAgreementMethods() as $paymentMethod) {
                $this->_paymentMethods[$paymentMethod->getCode()] = $paymentMethod->getTitle();
            }
        }
        return $this->_paymentMethods;
    }

    /**
     * Retrieve wizard payment options array
     *
     * @return array
     */
    public function getWizardPaymentMethodOptions()
    {
        $paymentMethodOptions = array();
        foreach ($this->_paymentHelper->getBillingAgreementMethods() as $paymentMethod) {
            if ($paymentMethod->getConfigData('allow_billing_agreement_wizard') == 1) {
                $paymentMethodOptions[$paymentMethod->getCode()] = $paymentMethod->getTitle();
            }
        }
        return $paymentMethodOptions;
    }

    /**
     * Set data to block
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setCreateUrl($this->getUrl('*/billing_agreement/startWizard'));
        return parent::_toHtml();
    }
}
