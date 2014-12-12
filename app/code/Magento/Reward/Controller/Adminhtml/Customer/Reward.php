<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Reward admin customer controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Controller\Adminhtml\Customer;

class Reward extends \Magento\Backend\App\Action
{
    /**
     * Check if module functionality enabled
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->_objectManager->get(
            'Magento\Reward\Helper\Data'
        )->isEnabled() && $request->getActionName() != 'noroute'
        ) {
            $this->_forward('noroute');
        }
        return parent::dispatch($request);
    }

    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(\Magento\Reward\Helper\Data::XML_PATH_PERMISSION_BALANCE);
    }
}
