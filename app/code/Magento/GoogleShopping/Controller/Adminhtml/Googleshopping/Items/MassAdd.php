<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Controller\Adminhtml\Googleshopping\Items;

class MassAdd extends \Magento\GoogleShopping\Controller\Adminhtml\Googleshopping\Items
{
    /**
     * Add (export) several products to Google Content
     *
     * @return void
     */
    public function execute()
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
            $this->_objectManager->create(
                'Magento\GoogleShopping\Model\MassOperations'
            )->setFlag(
                $flag
            )->addProducts(
                $productIds,
                $storeId
            );
        } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
            // Google requires CAPTCHA for login
            $this->messageManager->addError(__($e->getMessage()));
            $flag->unlock();
            $this->_redirectToCaptcha($e);
            return;
        } catch (\Exception $e) {
            $flag->unlock();
            $notifier->addMajor(
                __('An error has occurred while adding products to google shopping account.'),
                $e->getMessage()
            );
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            return;
        }

        $flag->unlock();
    }
}
