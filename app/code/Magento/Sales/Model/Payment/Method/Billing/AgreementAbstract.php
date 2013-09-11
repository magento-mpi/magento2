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
 * Sales Billing Agreement Payment Method Abstract model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Payment\Method\Billing;

abstract class AgreementAbstract extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Transport billing agreement id
     *
     */
    const TRANSPORT_BILLING_AGREEMENT_ID = 'ba_agreement_id';
    const PAYMENT_INFO_REFERENCE_ID      = 'ba_reference_id';

    protected $_infoBlockType = '\Magento\Sales\Block\Payment\Info\Billing\Agreement';
    protected $_formBlockType = '\Magento\Sales\Block\Payment\Form\Billing\Agreement';

    /**
     * Is method instance available
     *
     * @var null|bool
     */
    protected $_isAvailable = null;

    /**
     * Check whether method is available
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (is_null($this->_isAvailable)) {
            if (is_object($quote) && $quote->getCustomer()) {
                $availableBA = \Mage::getModel('Magento\Sales\Model\Billing\Agreement')->getAvailableCustomerBillingAgreements(
                    $quote->getCustomer()->getId()
                );
                $isAvailableBA = count($availableBA) > 0;
                $this->_canUseCheckout = $this->_canUseInternal = $isAvailableBA;
            }
            $this->_isAvailable = parent::isAvailable($quote) && $this->_isAvailable($quote);
            $this->_canUseCheckout = ($this->_isAvailable && $this->_canUseCheckout);
            $this->_canUseInternal = ($this->_isAvailable && $this->_canUseInternal);
        }
        return $this->_isAvailable;
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  \Magento\Payment\Model\Info
     */
    public function assignData($data)
    {
        $result = parent::assignData($data);

        $key = self::TRANSPORT_BILLING_AGREEMENT_ID;
        $id = false;
        if (is_array($data) && isset($data[$key])) {
            $id = $data[$key];
        } elseif ($data instanceof \Magento\Object && $data->getData($key)) {
            $id = $data->getData($key);
        }
        if ($id) {
            $info = $this->getInfoInstance();
            $ba = \Mage::getModel('Magento\Sales\Model\Billing\Agreement')->load($id);
            if ($ba->getId() && $ba->getCustomerId() == $info->getQuote()->getCustomer()->getId()) {
                $info->setAdditionalInformation($key, $id)
                    ->setAdditionalInformation(self::PAYMENT_INFO_REFERENCE_ID, $ba->getReferenceId());
            }
        }
        return $result;
    }

    /**
     *
     *
     * @param unknown_type $quote
     */
    abstract protected function _isAvailable($quote);
}
