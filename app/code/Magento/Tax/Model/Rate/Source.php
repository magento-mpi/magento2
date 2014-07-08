<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Rate;

use Magento\Tax\Service\V1\TaxRateServiceInterface;

/**
 * Tax rate source model.
 */
class Source implements \Magento\Framework\Data\OptionSourceInterface
{
    /** @var array */
    protected $options;

    /** @var TaxRateServiceInterface */
    protected $taxRateService;

    /**
     * TODO: Rate factory usage is temporary and rate service should be used as soon as search method is implemented
     *
     * @var \Magento\Tax\Model\Calculation\RateFactory
     */
    protected $rateFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Tax\Model\Calculation\RateFactory $rateFactory
     * @param TaxRateServiceInterface $taxRateService
     */
    public function __construct(
        \Magento\Tax\Model\Calculation\RateFactory $rateFactory,
        TaxRateServiceInterface $taxRateService
    ) {
        $this->taxRateService = $taxRateService;
        $this->rateFactory = $rateFactory;
    }

    /**
     * Retrieve all customer tax rates as an options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->rateFactory->create()->getCollection()->toOptionArray();
        }
        return $this->options;
    }
}
