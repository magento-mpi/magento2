<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\OfflinePayments\Model;

class Purchaseorder extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * @var string
     */
    protected $_code = 'purchaseorder';

    /**
     * @var string
     */
    protected $_formBlockType = 'Magento\OfflinePayments\Block\Form\Purchaseorder';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Magento\OfflinePayments\Block\Info\Purchaseorder';

    /**
     * Assign data to info model instance
     *
     * @param \Magento\Object|mixed $data
     * @return $this
     */
    public function assignData($data)
    {
        if (!$data instanceof \Magento\Object) {
            $data = new \Magento\Object($data);
        }

        $this->getInfoInstance()->setPoNumber($data->getPoNumber());
        return $this;
    }
}
