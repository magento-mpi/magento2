<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller;

use Magento\Rma\Model\Rma;

class Guest extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Check order view availability
     *
     * @param   \Magento\Rma\Model\Rma $rma
     * @return  bool
     */
    protected function _canViewRma($rma)
    {
        $currentOrder = $this->_coreRegistry->registry('current_order');
        if ($rma->getOrderId() && $rma->getOrderId() === $currentOrder->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Try to load valid rma by entity_id and register it
     *
     * @param int $entityId
     * @return void|bool
     */
    protected function _loadValidRma($entityId = null)
    {
        if (!$this->_objectManager->get(
            'Magento\Rma\Helper\Data'
        )->isEnabled() || !$this->_objectManager->get(
            'Magento\Sales\Helper\Guest'
        )->loadValidOrder(
            $this->_request,
            $this->_response
        )
        ) {
            return;
        }

        if (null === $entityId) {
            $entityId = (int)$this->getRequest()->getParam('entity_id');
        }

        if (!$entityId) {
            $this->_forward('noroute');
            return false;
        }
        /** @var $rma \Magento\Rma\Model\Rma */
        $rma = $this->_objectManager->create('Magento\Rma\Model\Rma')->load($entityId);

        if ($this->_canViewRma($rma)) {
            $this->_coreRegistry->register('current_rma', $rma);
            return true;
        } else {
            $this->_redirect('*/*/returns');
        }
        return false;
    }
}
