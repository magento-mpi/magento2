<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class ProcessOrderCreationData
{
    /**
     * Reward helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * @var \Magento\Reward\Model\PaymentDataImporter
     */
    protected $importer;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Reward\Model\PaymentDataImporter $importer
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Reward\Model\PaymentDataImporter $importer
    ) {
        $this->_rewardData = $rewardData;
        $this->importer = $importer;
    }

    /**
     * Payment data import in admin order create process
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $quote \Magento\Sales\Model\Quote */
        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
        if ($this->_rewardData->isEnabledOnFront($quote->getStore()->getWebsiteId())) {
            $request = $observer->getEvent()->getRequest();
            if (isset($request['payment']) && isset($request['payment']['use_reward_points'])) {
                $this->importer->import($quote, $quote->getPayment(), $request['payment']['use_reward_points']);
            }
        }
        return $this;
    }
}
