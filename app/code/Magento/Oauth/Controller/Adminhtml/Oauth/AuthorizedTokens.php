<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Manage authorized tokens controller
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Controller_Adminhtml_Oauth_AuthorizedTokens extends Magento_Adminhtml_Controller_Action
{
    /**
     * Init titles
     *
     * @return Magento_Oauth_Controller_Adminhtml_Oauth_AuthorizedTokens
     */
    public function preDispatch()
    {
        $this ->_title(__('Authorized Tokens'));
        parent::preDispatch();
        return $this;
    }

    /**
     * Render grid page
     */
    public function indexAction()
    {
        $this->loadLayout()->_setActiveMenu('Magento_Oauth::system_legacy_api_oauth_authorized_tokens');
        $this->renderLayout();
    }

    /**
     * Render grid AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Update revoke status action
     */
    public function revokeAction()
    {
        $ids = $this->getRequest()->getParam('items');
        $status = $this->getRequest()->getParam('status');

        if (!is_array($ids) || !$ids) {
            // No rows selected
            $this->_getSession()->addError(__('Please select needed row(s).'));
            $this->_redirect('*/*/index');
            return;
        }

        if (null === $status) {
            // No status selected
            $this->_getSession()->addError(__('Please select revoke status.'));
            $this->_redirect('*/*/index');
            return;
        }

        try {
            /** @var $collection Magento_Oauth_Model_Resource_Token_Collection */
            $collection = Mage::getModel('Magento_Oauth_Model_Token')->getCollection();
            $collection->joinConsumerAsApplication()
                    ->addFilterByType(Magento_Oauth_Model_Token::TYPE_ACCESS)
                    ->addFilterById($ids)
                    ->addFilterByRevoked(!$status);

            /** @var $item Magento_Oauth_Model_Token */
            foreach ($collection as $item) {
                $item->load($item->getId());
                $item->setRevoked($status)->save();

                $this->_sendTokenStatusChangeNotification($item, $status ? __('revoked') : __('enabled'));
            }
            if ($status) {
                $message = __('Selected entries revoked.');
            } else {
                $message = __('Selected entries enabled.');
            }
            $this->_getSession()->addSuccess($message);
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError(__('An error occurred on update revoke status.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        $ids = $this->getRequest()->getParam('items');

        if (!is_array($ids) || !$ids) {
            // No rows selected
            $this->_getSession()->addError(__('Please select needed row(s).'));
            $this->_redirect('*/*/index');
            return;
        }

        try {
            /** @var $collection Magento_Oauth_Model_Resource_Token_Collection */
            $collection = Mage::getModel('Magento_Oauth_Model_Token')->getCollection();
            $collection->joinConsumerAsApplication()
                    ->addFilterByType(Magento_Oauth_Model_Token::TYPE_ACCESS)
                    ->addFilterById($ids);

            /** @var $item Magento_Oauth_Model_Token */
            foreach ($collection as $item) {
                $item->delete();

                $this->_sendTokenStatusChangeNotification($item, __('deleted'));
            }
            $this->_getSession()->addSuccess(__('Selected entries has been deleted.'));
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError(__('An error occurred on delete action.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Oauth::authorizedTokens');
    }

    /**
     * Send email notification to user about token status change
     *
     * @param Magento_Oauth_Model_Token $token Token object
     * @param string $newStatus Name of new token status
     */
    protected function _sendTokenStatusChangeNotification($token, $newStatus)
    {
        if (($adminId = $token->getAdminId())) {
            /** @var $session Magento_Backend_Model_Auth_Session */
            $session = Mage::getSingleton('Magento_Backend_Model_Auth_Session');

            /** @var $admin Magento_User_Model_User */
            $admin = $session->getUser();

            if ($admin->getId() == $adminId) { // skip own tokens
                return;
            }
            $email = $admin->getEmail();
            $name  = $admin->getName(' ');
        } else {
            /** @var $customer Magento_Customer_Model_Customer */
            $customer = Mage::getModel('Magento_Customer_Model_Customer');

            $customer->load($token->getCustomerId());

            $email = $customer->getEmail();
            $name  = $customer->getName();
        }
        /** @var $helper Magento_Oauth_Helper_Data */
        $helper = Mage::helper('Magento_Oauth_Helper_Data');

        $helper->sendNotificationOnTokenStatusChange($email, $name, $token->getConsumer()->getName(), $newStatus);
    }
}
