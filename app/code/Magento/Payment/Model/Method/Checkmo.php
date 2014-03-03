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

class Checkmo extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * @var string
     */
    protected $_code  = 'checkmo';

    /**
     * @var string
     */
    protected $_formBlockType = 'Magento\Payment\Block\Form\Checkmo';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Magento\Payment\Block\Info\Checkmo';

    /**
     * Assign data to info model instance
     *
     * @param mixed $data
     * @return $this
     */
    public function assignData($data)
    {
        $details = array();
        if ($this->getPayableTo()) {
            $details['payable_to'] = $this->getPayableTo();
        }
        if ($this->getMailingAddress()) {
            $details['mailing_address'] = $this->getMailingAddress();
        }
        if (!empty($details)) {
            $this->getInfoInstance()->setAdditionalData(serialize($details));
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPayableTo()
    {
        return $this->getConfigData('payable_to');
    }

    /**
     * @return string
     */
    public function getMailingAddress()
    {
        return $this->getConfigData('mailing_address');
    }

}
