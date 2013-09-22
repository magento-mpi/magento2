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
 * oAuth My Applications controller
 *
 * Tab "My Applications" in the Customer Account
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Oauth\Controller\Customer;

class Token extends \Magento\Core\Controller\Front\Action
{
    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * Customer session model
     *
     * @var string
     */
    protected $_sessionName = 'Magento\Customer\Model\Session';

    /**
     * Check authentication
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_session = \Mage::getSingleton($this->_sessionName);
        if (!$this->_session->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }

    }

    /**
     * Render grid page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
    }

    /**
     * Redirect to referrer URL or otherwise to index page without params
     *
     * @return \Magento\Oauth\Controller\Customer\Token
     */
    protected function _redirectBack()
    {
        $url = $this->_getRefererUrl();
        if (\Mage::app()->getStore()->getBaseUrl() == $url) {
            $url = \Mage::getUrl('*/*/index');
        }
        $this->_redirectUrl($url);
        return $this;
    }

    /**
     * Update revoke status action
     */
    public function revokeAction()
    {
        $id = $this->getRequest()->getParam('id');
        $status = $this->getRequest()->getParam('status');

        if (0 === (int) $id) {
            // No ID
            $this->_session->addError(__('Invalid entry ID.'));
            $this->_redirectBack();
            return;
        }

        if (null === $status) {
            // No status selected
            $this->_session->addError(__('Invalid revoke status.'));
            $this->_redirectBack();
            return;
        }

        try {
            /** @var $collection \Magento\Oauth\Model\Resource\Token\Collection */
            $collection = \Mage::getModel('Magento\Oauth\Model\Token')->getCollection();
            $collection->joinConsumerAsApplication()
                    ->addFilterByCustomerId($this->_session->getCustomerId())
                    ->addFilterById($id)
                    ->addFilterByType(\Magento\Oauth\Model\Token::TYPE_ACCESS)
                    ->addFilterByRevoked(!$status);
            //here is can be load from model, but used from collection for get consumer name

            /** @var $model \Magento\Oauth\Model\Token */
            $model = $collection->getFirstItem();
            if ($model->getId()) {
                $name = $model->getName();
                $model->load($model->getId());
                $model->setRevoked($status)->save();
                if ($status) {
                    $message = __('Application "%1" has been revoked.', $name);
                } else {
                    $message = __('Application "%1" has been enabled.', $name);
                }
                $this->_session->addSuccess($message);
            } else {
                $this->_session->addError(__('Application not found.'));
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_session->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_session->addError(__('An error occurred on update revoke status.'));
            $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
        }
        $this->_redirectBack();
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');

        if (0 === (int) $id) {
            // No ID
            $this->_session->addError(__('Invalid entry ID.'));
            $this->_redirectBack();
            return;
        }

        try {
            /** @var $collection \Magento\Oauth\Model\Resource\Token\Collection */
            $collection = \Mage::getModel('Magento\Oauth\Model\Token')->getCollection();
            $collection->joinConsumerAsApplication()
                    ->addFilterByCustomerId($this->_session->getCustomerId())
                    ->addFilterByType(\Magento\Oauth\Model\Token::TYPE_ACCESS)
                    ->addFilterById($id);

            /** @var $model \Magento\Oauth\Model\Token */
            $model = $collection->getFirstItem();
            if ($model->getId()) {
                $name = $model->getName();
                $model->delete();
                $this->_session->addSuccess(
                    __('Application "%1" has been deleted.', $name));
            } else {
                $this->_session->addError(__('Application not found.'));
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_session->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_session->addError(__('An error occurred on delete application.'));
            $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
        }
        $this->_redirectBack();
    }
}
