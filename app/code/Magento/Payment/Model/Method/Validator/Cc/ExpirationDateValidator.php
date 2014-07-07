<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Payment\Model\Method\Validator\Cc;

use Magento\Payment\Model\Method\ValidatorInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Framework\Model\Exception;

class ExpirationDateValidator implements ValidatorInterface
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate)
    {
        $this->_localeDate = $localeDate;
    }

    /**
     * Validates payment method
     *
     * @param AbstractMethod $paymentMethod
     * @throws \Magento\Framework\Model\Exception
     * @return void
     */
    public function validate(AbstractMethod $paymentMethod)
    {
        $info = $paymentMethod->getInfoInstance();

        if ($info->getCcType() != 'SS' && !$this->_validateExpDate($info->getCcExpYear(), $info->getCcExpMonth())) {
            throw new Exception(__('We found an incorrect credit card expiration date.'));
        }
    }

    /**
     * @param string $expYear
     * @param string $expMonth
     * @return bool
     */
    protected function _validateExpDate($expYear, $expMonth)
    {
        $date = $this->_localeDate->date();
        if (!$expYear || !$expMonth
            || in_array($date->compareYear($expYear), [0 ,1]) && $date->compareMonth($expMonth ) == 1) {
            return false;
        }
        return true;
    }
}
