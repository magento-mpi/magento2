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

class Reward extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Check if module functionality enabled
     *
     * @return \Magento\Reward\Controller\Adminhtml\Reward\Rate
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_objectManager->get('Magento\Reward\Helper\Data')->isEnabled()
            && $this->getRequest()->getActionName() != 'noroute'
        ) {
            $this->_forward('noroute');
        }
        return $this;
    }

    /**
     * History Ajax Action
     */
    public function historyAction()
    {
        $this->loadLayout(false)
            ->renderLayout();
    }

    /**
     * History Grid Ajax Action
     *
     */
    public function historyGridAction()
    {
        $this->loadLayout(false)
            ->renderLayout();
    }

    /**
     *  Delete orphan points Action
     */
    public function deleteOrphanPointsAction()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        if ($customerId) {
            try {
                $this->_objectManager->create('Magento\Reward\Model\Reward')
                    ->deleteOrphanPointsByCustomer($customerId);
                $this->_getSession()
                    ->addSuccess(__('You removed the orphan points.'));
            } catch (\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/customer/edit', array('_current' => true));
    }

    /**
     * Acl check for admin
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(\Magento\Reward\Helper\Data::XML_PATH_PERMISSION_BALANCE);
    }
}
