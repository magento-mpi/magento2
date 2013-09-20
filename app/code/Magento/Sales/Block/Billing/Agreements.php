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
class Magento_Sales_Block_Billing_Agreements extends Magento_Core_Block_Template
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
     * @var Magento_Sales_Model_Resource_Billing_Agreement_Collection
     */
    protected $_billingAgreements = null;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Sales_Model_Resource_Billing_Agreement_CollectionFactory
     */
    protected $_agreementCollection;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Sales_Model_Resource_Billing_Agreement_CollectionFactory $agreementCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Sales_Model_Resource_Billing_Agreement_CollectionFactory $agreementCollection,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_agreementCollection = $agreementCollection;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Set Billing Agreement instance
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('Magento_Page_Block_Html_Pager')
            ->setCollection($this->getBillingAgreements())->setIsOutputRequired(false);
        $this->setChild('pager', $pager)
            ->setBackUrl($this->getUrl('customer/account/'));
        $this->getBillingAgreements()->load();
        return $this;
    }

    /**
     * Retrieve billing agreements collection
     *
     * @return Magento_Sales_Model_Resource_Billing_Agreement_Collection
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
     * @param Magento_Object|Magento_Sales_Model_Billing_Agreement $item
     * @param string $key
     * @return mixed
     */
    public function getItemValue(Magento_Sales_Model_Billing_Agreement $item, $key)
    {
        switch ($key) {
            case 'created_at':
            case 'updated_at':
                $value = ($item->getData($key))
                    ? $this->helper('Magento_Core_Helper_Data')->formatDate($item->getData($key), 'short', true)
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
            foreach ($this->helper('Magento_Payment_Helper_Data')->getBillingAgreementMethods() as $paymentMethod) {
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
        foreach ($this->helper('Magento_Payment_Helper_Data')->getBillingAgreementMethods() as $paymentMethod) {
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
