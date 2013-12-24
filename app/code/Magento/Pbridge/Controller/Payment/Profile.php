<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Saved Payment (CC profiles) controller
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Controller\Payment;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Profile extends \Magento\App\Action\Action
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Construct
     *
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Check whether Payment Profiles functionality enabled
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\Pbridge\Helper\Data')->arePaymentProfilesEnables()) {
            if ($request->getActionName() != 'noroute') {
                $this->_forward('noroute');
            }
        }
        return parent::dispatch($request);
    }

    /**
     * Payment Bridge frame with Saved Payment profiles
     */
    public function indexAction()
    {
        if(!$this->_customerSession->getCustomerId()) {
            $this->_customerSession->authenticate($this);
            return;
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
