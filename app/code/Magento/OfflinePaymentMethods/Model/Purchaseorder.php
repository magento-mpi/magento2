<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\OfflinePaymentMethods\Model;

class Purchaseorder extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code  = 'purchaseorder';
    protected $_formBlockType = 'Magento\OfflinePaymentMethods\Block\Form\Purchaseorder';
    protected $_infoBlockType = 'Magento\OfflinePaymentMethods\Block\Info\Purchaseorder';

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  \Magento\OfflinePaymentMethods\Model\Purchaseorder
     */
    public function assignData($data)
    {
        if (!($data instanceof \Magento\Object)) {
            $data = new \Magento\Object($data);
        }

        $this->getInfoInstance()->setPoNumber($data->getPoNumber());
        return $this;
    }
}
