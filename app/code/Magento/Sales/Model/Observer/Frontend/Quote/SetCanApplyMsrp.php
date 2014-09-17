<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Observer\Frontend\Quote;

use Magento\Catalog\Helper\Data as CatalogData;

/**
 * Class SetCanApplyMsrp
 */
class SetCanApplyMsrp
{
    /**
     * Catalog data
     *
     * @var CatalogData
     */
    protected $catalogData;

    /**
     * @param CatalogData $catalogData
     */
    public function __construct(CatalogData $catalogData)
    {
        $this->catalogData = $catalogData;
    }

    /**
     * Set Quote information about MSRP price enabled
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $observer->getEvent()->getQuote();

        $canApplyMsrp = false;
        if ($this->catalogData->isMsrpEnabled()) {
            foreach ($quote->getAllAddresses() as $address) {
                if ($address->getCanApplyMsrp()) {
                    $canApplyMsrp = true;
                    break;
                }
            }
        }

        $quote->setCanApplyMsrp($canApplyMsrp);
    }
}
