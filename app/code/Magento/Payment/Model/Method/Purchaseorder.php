<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Payment\Model\Method;

class Purchaseorder extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code  = 'purchaseorder';
    protected $_formBlockType = 'Magento\Payment\Block\Form\Purchaseorder';
    protected $_infoBlockType = 'Magento\Payment\Block\Info\Purchaseorder';

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  \Magento\Payment\Model\Method\Purchaseorder
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
