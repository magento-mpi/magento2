<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\TaxDetails;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Tax\Api\Data\AppliedTaxRateInterface;

class AppliedTaxRate extends AbstractExtensibleModel implements AppliedTaxRateInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(AppliedTaxRateInterface::KEY_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(AppliedTaxRateInterface::KEY_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getPercent()
    {
        return $this->getData(AppliedTaxRateInterface::KEY_PERCENT);
    }
}
