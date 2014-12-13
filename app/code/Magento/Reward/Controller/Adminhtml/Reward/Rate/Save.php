<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Controller\Adminhtml\Reward\Rate;

use Magento\Framework\App\ResponseInterface;

class Save extends \Magento\Reward\Controller\Adminhtml\Reward\Rate
{
    /**
     * Save Action
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('rate');

        if ($data) {
            $rate = $this->_initRate();

            if ($this->getRequest()->getParam('rate_id') && !$rate->getId()) {
                return $this->_redirect('adminhtml/*/');
            }

            $rate->addData($data);

            try {
                $rate->save();
                $this->messageManager->addSuccess(__('You saved the rate.'));
            } catch (\Exception $exception) {
                $this->_objectManager->get('Magento\Framework\Logger')->logException($exception);
                $this->messageManager->addError(__('We cannot save Rate.'));
                return $this->_redirect('adminhtml/*/edit', ['rate_id' => $rate->getId(), '_current' => true]);
            }
        }

        return $this->_redirect('adminhtml/*/');
    }
}
