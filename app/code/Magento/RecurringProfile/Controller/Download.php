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
    protected $_download;

    public function __construct(\Magento\App\Action\Context $context, \Magento\Sales\Model\Download $download)
    {
        $this->_download = $download;
        parent::__construct($context);
    }

    /**
     * Profile custom options download action
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
            $request = unserialize($orderItemInfo['info_buyRequest']);

            if ($request['product'] != $orderItemInfo['product_id']) {
                $this->_forward('noroute');
                return;
            }

            $optionId = $this->getRequest()->getParam('option_id');
            if (!isset($request['options'][$optionId])) {
                $this->_forward('noroute');
                return;
            }
            // Check if the product exists
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($request['product']);
            if (!$product || !$product->getId()) {
                $this->_forward('noroute');
                return;
            }
            // Try to load the option
            $option = $product->getOptionById($optionId);
            if (!$option || !$option->getId() || $option->getType() != 'file') {
                $this->_forward('noroute');
                return;
            }
            $info = $request['options'][$this->getRequest()->getParam('option_id')];
            if ($this->getRequest()->getParam('key') != $info['secret_key']) {
                $this->_forward('noroute');
                return;
            }
            $this->_download->downloadFile($info);
        } catch (\Exception $e) {
            $this->_forward('noroute');
        }
    }
}
