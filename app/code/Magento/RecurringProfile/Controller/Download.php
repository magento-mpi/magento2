<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Controller;

class Download extends \Magento\App\Action\Action
{
    /**
     * @var \Magento\Sales\Model\Download
     */
    protected $download;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Sales\Model\Download $download
     */
    public function __construct(\Magento\App\Action\Context $context, \Magento\Sales\Model\Download $download)
    {
        parent::__construct($context);
        $this->download = $download;
    }

    /**
     * Profile custom options download action
     *
     * @return void
     */
    public function downloadProfileCustomOptionAction()
    {
        $recurringProfile = $this->_objectManager->create('Magento\RecurringProfile\Model\Profile')
            ->load($this->getRequest()->getParam('id'));

        if (!$recurringProfile->getId()) {
            $this->_forward('noroute');
        }

        $orderItemInfo = $recurringProfile->getData('order_item_info');
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
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($buyRequest['product']);
        if (!$product->getId()) {
            throw new \Exception();
        }
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
}
