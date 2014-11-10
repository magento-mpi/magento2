<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Controller\Download;

class DownloadPaymentCustomOption extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Model\Download
     */
    protected $download;

    /** @var \Magento\Catalog\Model\ProductRepository */
    protected $productRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Sales\Model\Download $download
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\Download $download,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        parent::__construct($context);
        $this->download = $download;
        $this->productRepository = $productRepository;
    }

    /**
     * Retrieve custom option information
     *
     * @param array $buyRequest
     * @return array
     * @throws \Exception
     */
    protected function getOptionInfo($buyRequest)
    {
        $optionId = $this->getRequest()->getParam('option_id');
        if (!isset($buyRequest['options'][$optionId])) {
            throw new \Exception();
        }
        $product = $this->productRepository->getById($buyRequest['product']);
        $option = $product->getOptionById($optionId);
        if (!$option || !$option->getId() || $option->getType() != 'file') {
            throw new \Exception();
        }
        $info = $buyRequest['options'][$this->getRequest()->getParam('option_id')];
        if ($this->getRequest()->getParam('key') != $info['secret_key']) {
            throw new \Exception();
        }
        return $info;
    }

    /**
     * Payment custom options download action
     *
     * @return void
     */
    public function execute()
    {
        $recurringPayment = $this->_objectManager->create(
            'Magento\RecurringPayment\Model\Payment'
        )->load(
            $this->getRequest()->getParam('id')
        );

        if (!$recurringPayment->getId()) {
            $this->_forward('noroute');
        }

        $orderItemInfo = $recurringPayment->getData('order_item_info');
        try {
            $buyRequest = unserialize($orderItemInfo['info_buyRequest']);
            if ($buyRequest['product'] != $orderItemInfo['product_id']) {
                throw new \Exception();
            }
            $this->download->downloadFile($this->getOptionInfo($buyRequest));
        } catch (\Exception $e) {
            $this->_forward('noroute');
        }
    }
}
