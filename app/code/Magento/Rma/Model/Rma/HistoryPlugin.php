<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model\Rma;

use Magento\Rma\Model\Rma\Status\History;
use Magento\Rma\Model\Rma;
use Magento\Framework\App\RequestInterface;
use Magento\Rma\Model\Rma\Status\HistoryFactory;

/**
 * Class HistoryPlugin
 */
class HistoryPlugin
{
    /**
     * Rma conformation flag
     */
    const RMA_CONFIRMATION = 'rma_confirmation';

    /**
     * @var bool
     */
    protected $rmaConfirmation;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param HistoryFactory $historyFactory
     * @param RequestInterface $request
     */
    public function __construct(
        HistoryFactory $historyFactory,
        RequestInterface $request
    ) {
        $this->historyFactory = $historyFactory;
        $this->request = $request;
    }

    /**
     * Before saveRma plugin
     *
     * @param Rma $subject
     * @param array $data
     * @return array
     */
    public function beforeSaveRma(Rma $subject, $data)
    {
        $this->rmaConfirmation = (bool)$this->request->getParam(self::RMA_CONFIRMATION);
        return [$data];
    }

    /**
     * After save plugin
     *
     * @param Rma $subject
     * @param Rma $result
     * @return array
     */
    public function afterSave(Rma $subject, $result)
    {
        /** @var $history  History */
        $history = $this->historyFactory->create();
        $history->setRma($subject);
        $history->setIsCustomerNotified($this->rmaConfirmation);
        $history->saveSystemComment();
        return [$result];
    }
}
