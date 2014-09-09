<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class PaymentDataImport
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
     * Payment data import in checkout process
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_rewardData->isEnabledOnFront()) {
            $input = $observer->getEvent()->getInput();
            /* @var $quote \Magento\Sales\Model\Quote */
            $quote = $observer->getEvent()->getPayment()->getQuote();
            $this->importer->import($quote, $input, $input->getUseRewardPoints());
        }
        return $this;
    }
}
