<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Controller\Adminhtml\Reward;

use Magento\App\ResponseInterface;

/**
 * Reward admin rate controller
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rate extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Check if module functionality enabled
     *
     * @param \Magento\App\RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(\Magento\App\RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\Reward\Helper\Data')->isEnabled()
            && $request->getActionName() != 'noroute'
        ) {
            $this->_forward('noroute');
        }
        return parent::dispatch($request);
    }

    /**
     * Initialize layout, breadcrumbs
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Reward::customer_reward')
            ->_addBreadcrumb(__('Customers'),
                __('Customers'))
            ->_addBreadcrumb(__('Manage Reward Exchange Rates'),
                __('Manage Reward Exchange Rates'));
        return $this;
    }

    /**
     * Initialize rate object
     *
     * @return \Magento\Reward\Model\Reward\Rate
     */
    protected function _initRate()
    {
        $this->_title->add(__('Reward Exchange Rates'));

        $rateId = $this->getRequest()->getParam('rate_id', 0);
        $rate = $this->_objectManager->create('Magento\Reward\Model\Reward\Rate');
        if ($rateId) {
            $rate->load($rateId);
        }
        $this->_coreRegistry->register('current_reward_rate', $rate);
        return $rate;
    }

    /**
     * Index Action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Reward Exchange Rates'));

        $this->_initAction();
        $this->_view->renderLayout();
    }

    /**
     * New Action.
     * Forward to Edit Action
     *
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit Action
     *
     * @return void
     */
    public function editAction()
    {
        $rate = $this->_initRate();

        $this->_title->add($rate->getRateId() ? sprintf("#%s", $rate->getRateId()) : __('New Reward Exchange Rate'));

        $this->_initAction();
        $this->_view->renderLayout();
    }

    /**
     * Save Action
     *
     * @return ResponseInterface
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('rate');

        if ($data) {
            $rate = $this->_initRate();

            if ($this->getRequest()->getParam('rate_id') && ! $rate->getId()) {
                return $this->_redirect('adminhtml/*/');
            }

            $rate->addData($data);

            try {
                $rate->save();
                $this->messageManager->addSuccess(__('You saved the rate.'));
            } catch (\Exception $exception) {
                $this->_objectManager->get('Magento\Logger')->logException($exception);
                $this->messageManager->addError(__('We cannot save Rate.'));
                return $this->_redirect('adminhtml/*/edit', array('rate_id' => $rate->getId(), '_current' => true));
            }
        }

        return $this->_redirect('adminhtml/*/');
    }

    /**
     * Delete Action
     *
     * @return void
     */
    public function deleteAction()
    {
        $rate = $this->_initRate();
        if ($rate->getId()) {
            try {
                $rate->delete();
                $this->messageManager->addSuccess(__('You deleted the rate.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('_current' => true));
                return;
            }
        }

        return $this->_redirect('adminhtml/*/');
    }

    /**
     * Validate Action
     *
     * @return void
     */
    public function validateAction()
    {
        $response = new \Magento\Object(array('error' => false));
        $post     = $this->getRequest()->getParam('rate');
        $message  = null;
        /** @var \Magento\Core\Model\StoreManagerInterface $storeManager */
        $storeManager = $this->_objectManager->get('Magento\Core\Model\StoreManagerInterface');
        if ($storeManager->isSingleStoreMode()) {
            $post['website_id'] = $storeManager->getStore(true)->getWebsiteId();
        }

        if (!isset($post['customer_group_id'])
            || !isset($post['website_id'])
            || !isset($post['direction'])
            || !isset($post['value'])
            || !isset($post['equal_value'])) {
            $message = __('Please enter all Rate information.');
        } elseif ($post['direction'] == \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY
                  && ((int) $post['value'] <= 0 || (float) $post['equal_value'] <= 0)) {
              if ((int) $post['value'] <= 0) {
                  $message = __('Please enter a positive integer number in the left rate field.');
              } else {
                  $message = __('Please enter a positive number in the right rate field.');
              }
        } elseif ($post['direction'] == \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS
                  && ((float) $post['value'] <= 0 || (int) $post['equal_value'] <= 0)) {
              if ((int) $post['equal_value'] <= 0) {
                  $message = __('Please enter a positive integer number in the right rate field.');
              } else {
                  $message = __('Please enter a positive number in the left rate field.');
              }
        } else {
            $rate       = $this->_initRate();
            $isRateUnique = $rate->getIsRateUniqueToCurrent(
                $post['website_id'],
                $post['customer_group_id'],
                $post['direction']
            );

            if (!$isRateUnique) {
                $message = __('Sorry, but a rate with the same website, customer group and direction or covering rate already exists.');
            }
        }

        if ($message) {
            $this->messageManager->addError($message);
            $this->_view->getLayout()->initMessages();
            $response->setError(true);
            $response->setMessage($this->_view->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Reward::rates');
    }
}
