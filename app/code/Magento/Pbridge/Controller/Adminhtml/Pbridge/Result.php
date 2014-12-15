<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Controller\Adminhtml\Pbridge;

class Result extends \Magento\Pbridge\Controller\Adminhtml\Pbridge
{
    /**
     * Load only action layout handles
     *
     * @return $this
     */
    protected function _initActionLayout()
    {
        $this->_view->addActionLayoutHandles();
        $this->_view->loadLayoutUpdates();
        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();
        $this->_view->setIsLayoutLoaded(true);
        $this->_view->getLayout()->initMessages();
        return $this;
    }

    /**
     * Result Action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('store')) {
            $this->_objectManager->get(
                'Magento\Pbridge\Helper\Data'
            )->setStoreId(
                $this->getRequest()->getParam('store')
            );
        }
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }
}
