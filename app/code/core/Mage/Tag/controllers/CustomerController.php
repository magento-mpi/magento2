<?php
/**
 * Customer tags controller
 *
 * @package     Mage
 * @subpackage  Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_CustomerController extends Mage_Core_Controller_Varien_Action
{

    public function indexAction()
    {
        if( !Mage::getSingleton('customer/session')->getCustomerId() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $this->loadLayout(array('default', 'customer_account'), 'customer_account');

        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('root')->setHeaderTitle(__('My Account'));
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('tag/customer_tags'));

        $this->renderLayout();
    }

    public function viewAction()
    {
        if( !Mage::getSingleton('customer/session')->getCustomerId() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }
        Mage::register('tagId', $this->getRequest()->getParam('tagId'));

        $this->_initLayoutMessages('customer/session');
        $this->loadLayout(array('default', 'customer_account'), 'customer_account');

        $this->getLayout()->getBlock('root')->setHeaderTitle(__('My Account'));
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('tag/customer_view'));

        $this->renderLayout();
    }

    public function editAction()
    {
        if( !Mage::getSingleton('customer/session')->getCustomerId() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $this->loadLayout(array('default', 'customer_account'), 'customer_account');

        $tagId = $this->getRequest()->getParam('tagId');
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();

        $model = Mage::getModel('tag/tag_relation');
        $model->loadByTagCustomer(null, $tagId, $customerId);

        Mage::register('tagModel', $model);

        if( intval($tagId) <= 0 ) {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
            return;
        }

        if( $model->getCustomerId() != $customerId ) {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
            return;
        }

        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('root')->setHeaderTitle(__('My Account'));
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('tag/customer_edit'));

        $this->renderLayout();
    }

    public function removeAction()
    {
        if( !Mage::getSingleton('customer/session')->getCustomerId() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $tagId = $this->getRequest()->getParam('tagId');
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();

        if( intval($tagId) <= 0 ) {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
            return;
        }

        $model = Mage::getModel('tag/tag_relation');
        $model->loadByTagCustomer(null, $tagId, $customerId);
        if( $model->getCustomerId() == $customerId ) {
            $model->delete();
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
            return;
        } else {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
            return;
        }
    }

    public function saveAction()
    {
        if( !Mage::getSingleton('customer/session')->getCustomerId() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
            $this->getResponse()->setRedirect($referer);
        }

        if( $post = $this->getRequest()->getPost() ) {
            try {
                $tagId = $this->getRequest()->getParam('tagId');
                $customerId = Mage::getSingleton('customer/session')->getCustomerId();
                $tagName = $this->getRequest()->getParam('tagName');
                $productId = 0;

                $tagModel = Mage::getModel('tag/tag');
                $tagModel->load($tagId);
                $storeId = $tagModel->getStoreId();

                if( $tagModel->getName() != $tagName ) {
                    $tagModel->loadByName($tagName);

                    $tagModel->setName($tagName)
                            ->setStatus( ( $tagModel->getId() && $tagModel->getStatus() != $tagModel->getPendingStatus() ) ? $tagModel->getStatus() : $tagModel->getPendingStatus() )
                            ->setStoreId($storeId)
                            ->save();
                }

                $tagRalationModel = Mage::getModel('tag/tag_relation');
                $tagRalationModel->loadByTagCustomer(null, $tagId, $customerId);

                if( $tagRalationModel->getCustomerId() == $customerId ) {
                    $productId = $tagRalationModel->getProductId();
                    $tagRalationModel->delete();

                    $newTagRalationModel = Mage::getModel('tag/tag_relation')
                        ->setTagId($tagModel->getId())
                        ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                        ->setStoreId($storeId)
                        ->setProductId($productId)
                        ->save();
                }

                if( $tagModel->getId() ) {
                    $this->getResponse()->setRedirect(Mage::getUrl('*/*/view', array('tagId' => $tagModel->getId())));
                }
                return;
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
                return;
            }
        }
    }
}