<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Manage "My Applications" controller
 *
 * Applications for logged admin user
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Adminhtml_OAuth_Admin_TokenController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init titles
     *
     * @return Mage_OAuth_Adminhtml_OAuth_Admin_TokenController
     */
    public function preDispatch()
    {
        $this->_title($this->__('System'))
                ->_title($this->__('Permissions'))
                ->_title($this->__('My Applications'));
        parent::preDispatch();
        return $this;
    }

    /**
     * Render grid page
     */
    public function indexAction()
    {
        $this->loadLayout();
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
            $this->_getSession()->addError($this->__('Please select needed row(s).'));
            $this->_redirect('*/*/index');
            return;
        }

        if (null === $status) {
            // No status selected
            $this->_getSession()->addError($this->__('Please select revoke status.'));
            $this->_redirect('*/*/index');
            return;
        }

        try {
            /** @var $user Mage_Admin_Model_User */
            $user = Mage::getSingleton('admin/session')->getData('user');

            /** @var $collection Mage_OAuth_Model_Resource_Token_Collection */
            $collection = Mage::getModel('oauth/token')->getCollection();
            $collection->joinConsumerAsApplication()
                    ->addFilterByAdminId($user->getId())
                    ->addFilterByType(Mage_OAuth_Model_Token::TYPE_ACCESS)
                    ->addFilterById($ids)
                    ->addFilterByRevoked(!$status);

            /** @var $item Mage_OAuth_Model_Token */
            foreach ($collection as $item) {
                $item->load($item->getId());
                $item->setRevoked($status)->save();
            }
            if ($status) {
                $message = $this->__('Selected entries revoked.');
            } else {
                $message = $this->__('Selected entries enabled.');
            }
            $this->_getSession()->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('An error occurred on update revoke status.'));
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
            $this->_getSession()->addError($this->__('Please select needed row(s).'));
            $this->_redirect('*/*/index');
            return;
        }

        try {
            /** @var $user Mage_Admin_Model_User */
            $user = Mage::getSingleton('admin/session')->getData('user');

            /** @var $collection Mage_OAuth_Model_Resource_Token_Collection */
            $collection = Mage::getModel('oauth/token')->getCollection();
            $collection->joinConsumerAsApplication()
                    ->addFilterByAdminId($user->getId())
                    ->addFilterByType(Mage_OAuth_Model_Token::TYPE_ACCESS)
                    ->addFilterById($ids);

            /** @var $item Mage_OAuth_Model_Token */
            foreach ($collection as $item) {
                $item->delete();
            }
            $this->_getSession()->addSuccess($this->__('Selected entries has been deleted.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('An error occurred on delete action.'));
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
        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');
        return $session->isAllowed('system/acl/admin_token');
    }
}
