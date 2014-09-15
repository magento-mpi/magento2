<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Service\V1\Data\CreditmemoConverter;

/**
 * Class CreditmemoCreate
 */
class CreditmemoCreate
{
    /**
     * @var CreditmemoConverter
     */
    protected $invoiceConverter;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @param CreditmemoConverter $creditmemoConverter
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(CreditmemoConverter $creditmemoConverter, \Magento\Framework\Logger $logger)
    {
        $this->creditmemoConverter = $creditmemoConverter;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Service\V1\Data\Creditmemo $creditmemoDataObject
     * @throws \Exception
     * @return bool
     */
    public function invoke(\Magento\Sales\Service\V1\Data\Creditmemo $creditmemoDataObject)
    {
        try {
            /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
            $creditmemo = $this->creditmemoConverter->getModel($creditmemoDataObject);
            if (!$creditmemo) {
                return false;
            }
            if (!$creditmemo->isValidGrandTotal()) {
                return false;
            }
            $creditmemo->register();
            $creditmemo->save();
            return true;
        } catch (\Exception $e) {
            $this->logger->logException($e);
            throw new \Exception(__('An error has occurred during creating Creditmemo'));
        }
    }
}
