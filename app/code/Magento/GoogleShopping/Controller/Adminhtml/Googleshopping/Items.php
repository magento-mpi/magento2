<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleShopping Admin Items Controller
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @name       \Magento\GoogleShopping\Controller\Adminhtml\Googleshopping\Items
 * @author     Magento Core Team <core@magentocommerce.com>
*/
namespace Magento\GoogleShopping\Controller\Adminhtml\Googleshopping;

class Items extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\App\Action\Title
     */
    protected $_title;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\App\Action\Title $title
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\App\Action\Title $title
    )
    {
        parent::__construct($context);
        $this->_title = $title;
    }

    /**
     * Initialize general settings for action
     *
     * @return  \Magento\GoogleShopping\Controller\Adminhtml\Googleshopping\Items
     */
    protected function _initAction()
    {
        $this->_layoutServices->loadLayout();
        $this->_setActiveMenu('Magento_GoogleShopping::catalog_googleshopping_items')
            ->_addBreadcrumb(__('Catalog'), __('Catalog'))
            ->_addBreadcrumb(__('Google Content'), __('Google Content'));
        return $this;
    }

    /**
     * Manage Items page with two item grids: Magento products and Google Content items
     */
    public function indexAction()
    {
        $this->_title->add(__('Google Content Items'));

        if (0 === (int)$this->getRequest()->getParam('store')) {
            $this->_redirect('adminhtml/*/', array(
                'store' => $this->_objectManager->get('Magento\Core\Model\StoreManagerInterface')
                    ->getAnyStoreView()->getId(),
                '_current' => true)
            );
            return;
        }

        $this->_initAction();

        $contentBlock = $this->_layoutServices->getLayout()
            ->createBlock('Magento\GoogleShopping\Block\Adminhtml\Items')->setStore($this->_getStore());

        if ($this->getRequest()->getParam('captcha_token') && $this->getRequest()->getParam('captcha_url')) {
            $contentBlock->setGcontentCaptchaToken(
                $this->_objectManager->get('Magento\Core\Helper\Data')
                    ->urlDecode($this->getRequest()->getParam('captcha_token'))
            );
            $contentBlock->setGcontentCaptchaUrl(
                $this->_objectManager->get('Magento\Core\Helper\Data')
                    ->urlDecode($this->getRequest()->getParam('captcha_url'))
            );
        }

        if (!$this->_objectManager->get('Magento\GoogleShopping\Model\Config')
            ->isValidDefaultCurrencyCode($this->_getStore()->getId())
        ) {
            $_countryInfo = $this->_objectManager->get('Magento\GoogleShopping\Model\Config')
                ->getTargetCountryInfo($this->_getStore()->getId());
            $this->_getSession()->addNotice(
                __("The store's currency should be set to %1 for %2 in system configuration. Otherwise item prices won't be correct in Google Content.", $_countryInfo['currency_name'], $_countryInfo['name'])
            );
        }

        $this->_addBreadcrumb(__('Items'), __('Items'))
            ->_addContent($contentBlock);
        $this->_layoutServices->renderLayout();
    }

    /**
     * Grid with Google Content items
     */
    public function gridAction()
    {
        $this->_layoutServices->loadLayout();
        $this->getResponse()->setBody(
            $this->_layoutServices->getLayout()
                ->createBlock('Magento\GoogleShopping\Block\Adminhtml\Items\Item')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
           );
    }

    /**
     * Retrieve synchronization process mutex
     *
     * @return \Magento\GoogleShopping\Model\Flag
     */
    protected function _getFlag()
    {
        return $this->_objectManager->get('Magento\GoogleShopping\Model\Flag')->loadSelf();
    }

    /**
     * Add (export) several products to Google Content
     */
    public function massAddAction()
    {
        $flag = $this->_getFlag();
        if ($flag->isLocked()) {
            return;
        }

        session_write_close();
        ignore_user_abort(true);
        set_time_limit(0);

        $storeId = $this->_getStore()->getId();
        $productIds = $this->getRequest()->getParam('product', null);
        $notifier = $this->_objectManager->create('Magento\AdminNotification\Model\Inbox');

        try {
            $flag->lock();
            $this->_objectManager->create('Magento\GoogleShopping\Model\MassOperations')
                ->setFlag($flag)
                ->addProducts($productIds, $storeId);
        } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
            // Google requires CAPTCHA for login
            $this->_getSession()->addError(__($e->getMessage()));
            $flag->unlock();
            $this->_redirectToCaptcha($e);
            return;
        } catch (\Exception $e) {
            $flag->unlock();
            $notifier->addMajor(
                __('An error has occurred while adding products to google shopping account.'),
                $e->getMessage()
            );
            $this->_objectManager->get('Magento\Logger')->logException($e);
            return;
        }

        $flag->unlock();
    }

    /**
     * Delete products from Google Content
     */
    public function massDeleteAction()
    {
        $flag = $this->_getFlag();
        if ($flag->isLocked()) {
            return;
        }

        session_write_close();
        ignore_user_abort(true);
        set_time_limit(0);

        $itemIds = $this->getRequest()->getParam('item');

        try {
            $flag->lock();
            $this->_objectManager->create('Magento\GoogleShopping\Model\MassOperations')
                ->setFlag($flag)
                ->deleteItems($itemIds);
        } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
            // Google requires CAPTCHA for login
            $this->_getSession()->addError(__($e->getMessage()));
            $flag->unlock();
            $this->_redirectToCaptcha($e);
            return;
        } catch (\Exception $e) {
            $flag->unlock();
            $this->_objectManager->create('Magento\AdminNotification\Model\Inbox')->addMajor(
                __('An error has occurred while deleting products from google shopping account.'),
                __('One or more products were not deleted from google shopping account. Refer to the log file for details.')
            );
            $this->_objectManager->get('Magento\Logger')->logException($e);
            return;
        }

        $flag->unlock();
    }

    /**
     * Update items statistics and remove the items which are not available in Google Content
     */
    public function refreshAction()
    {
        $flag = $this->_getFlag();
        if ($flag->isLocked()) {
            return;
        }

        session_write_close();
        ignore_user_abort(true);
        set_time_limit(0);

        $itemIds = $this->getRequest()->getParam('item');

        try {
            $flag->lock();
            $this->_objectManager->create('Magento\GoogleShopping\Model\MassOperations')
                ->setFlag($flag)
                ->synchronizeItems($itemIds);
        } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
            // Google requires CAPTCHA for login
            $this->_getSession()->addError(__($e->getMessage()));
            $flag->unlock();
            $this->_redirectToCaptcha($e);
            return;
        } catch (\Exception $e) {
            $flag->unlock();
            $this->_objectManager->create('Magento\AdminNotification\Model\Inbox')->addMajor(
                __('An error has occurred while deleting products from google shopping account.'),
                __('One or more products were not deleted from google shopping account. Refer to the log file for details.')
            );
            $this->_objectManager->get('Magento\Logger')->logException($e);
            return;
        }

        $flag->unlock();
    }

    /**
     * Confirm CAPTCHA
     */
    public function confirmCaptchaAction()
    {

        $storeId = $this->_getStore()->getId();
        try {
            $this->_objectManager->create('Magento\GoogleShopping\Model\Service')->getClient(
                $storeId,
                $this->_objectManager->get('Magento\Core\Helper\Data')
                    ->urlDecode($this->getRequest()->getParam('captcha_token')),
                $this->getRequest()->getParam('user_confirm')
            );
            $this->_getSession()->addSuccess(__('Captcha has been confirmed.'));

        } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->_getSession()->addError(__('There was a Captcha confirmation error: %1', $e->getMessage()));
            $this->_redirectToCaptcha($e);
            return;
        } catch (\Zend_Gdata_App_Exception $e) {
            $this->_getSession()->addError(
                $this->_objectManager->get('Magento\GoogleShopping\Helper\Data')
                    ->parseGdataExceptionMessage($e->getMessage())
            );
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $this->_getSession()->addError(__('Something went wrong during Captcha confirmation.'));
        }

        $this->_redirect('adminhtml/*/index', array('store'=>$storeId));
    }

    /**
     * Retrieve background process status
     *
     * @return \Zend_Controller_Response_Abstract
     */
    public function statusAction()
    {
        if ($this->getRequest()->isAjax()) {
            $this->getResponse()->setHeader('Content-Type', 'application/json');
            $params = array(
                'is_running' => $this->_getFlag()->isLocked()
            );
            return $this->getResponse()->setBody(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($params)
            );
        }
    }

    /**
     * Redirect user to Google Captcha challenge
     *
     * @param \Zend_Gdata_App_CaptchaRequiredException $e
     */
    protected function _redirectToCaptcha($e)
    {
        $redirectUrl = $this->getUrl(
            '*/*/index',
            array(
                'store' => $this->_getStore()->getId(),
                'captcha_token' => $this->_objectManager->get('Magento\Core\Helper\Data')
                    ->urlEncode($e->getCaptchaToken()),
                'captcha_url' => $this->_objectManager->get('Magento\Core\Helper\Data')
                    ->urlEncode($e->getCaptchaUrl())
            )
        );
        if ($this->getRequest()->isAjax()) {
            $this->getResponse()->setHeader('Content-Type', 'application/json')
                ->setBody(
                    $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(
                        array('redirect' => $redirectUrl)
                    )
                );
        } else {
            $this->_redirect($redirectUrl);
        }
    }

    /**
     * Get store object, basing on request
     *
     * @return \Magento\Core\Model\Store
     * @throws \Magento\Core\Exception
     */
    public function _getStore()
    {
        $store = $this->_objectManager->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore((int)$this->getRequest()->getParam('store', 0));
        if ((!$store) || 0 == $store->getId()) {
            throw new \Magento\Core\Exception(__('Unable to select a Store View'));
        }
        return $store;
    }

    /**
     * Check access to this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GoogleShopping::items');
    }
}
