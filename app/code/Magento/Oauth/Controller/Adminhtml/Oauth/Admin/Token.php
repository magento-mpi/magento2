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
 * Manage "My Applications" controller
 *
 * Applications for logged admin user
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Controller_Adminhtml_Oauth_Admin_Token extends Magento_Adminhtml_Controller_Action
{
    /**
     * Perform layout initialization actions
     *
     * @return Magento_Oauth_Controller_Adminhtml_Oauth_Consumer
     */
    protected function  _initAction()
    {
        // TODO: Fix during Web API authentication implementation
        // $this->loadLayout()->_setActiveMenu('Magento_Oauth::system_legacy_api_oauth_admin_token');
        return $this;
    }

    /**
     * Init titles
     *
     * @return Magento_Oauth_Controller_Adminhtml_Oauth_Admin_Token
     */
    public function preDispatch()
    {
        $this->_title(__('My Applications'));
        parent::preDispatch();
        return $this;
    }

    /**
     * Render grid page
     */
    public function indexAction()
    {
        $this->_initAction();
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
            /** @var $user Magento_User_Model_User */
            $user = Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getData('user');

            /** @var $collection Magento_Oauth_Model_Resource_Token_Collection */
            $collection = Mage::getModel('Magento_Oauth_Model_Token')->getCollection();
            $collection->joinConsumerAsApplication()
                    ->addFilterByAdminId($user->getId())
                    ->addFilterByType(Magento_Oauth_Model_Token::TYPE_ACCESS)
                    ->addFilterById($ids)
                    ->addFilterByRevoked(!$status);

            /** @var $item Magento_Oauth_Model_Token */
            foreach ($collection as $item) {
                $item->load($item->getId());
                $item->setRevoked($status)->save();
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
            /** @var $user Magento_User_Model_User */
            $user = Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getData('user');

            /** @var $collection Magento_Oauth_Model_Resource_Token_Collection */
            $collection = Mage::getModel('Magento_Oauth_Model_Token')->getCollection();
            $collection->joinConsumerAsApplication()
                    ->addFilterByAdminId($user->getId())
                    ->addFilterByType(Magento_Oauth_Model_Token::TYPE_ACCESS)
                    ->addFilterById($ids);

            /** @var $item Magento_Oauth_Model_Token */
            foreach ($collection as $item) {
                $item->delete();
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
        return $this->_authorization->isAllowed('Magento_Oauth::oauth_admin_token');
    }
}
