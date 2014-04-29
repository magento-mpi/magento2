<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward admin customer controller
 *
 * @category    Magento
 * @package     Magento_Reward
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
     * History Ajax Action
     *
     * @return void
     */
    public function historyAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * History Grid Ajax Action
     *
     * @return void
     *
     */
    public function historyGridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     *  Delete orphan points Action
     *
     * @return void
     */
    public function deleteOrphanPointsAction()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        if ($customerId) {
            try {
                $this->_objectManager->create(
                    'Magento\Reward\Model\Reward'
                )->deleteOrphanPointsByCustomer(
                    $customerId
                );
                $this->messageManager->addSuccess(__('You removed the orphan points.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('customer/index/edit', array('_current' => true));
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
