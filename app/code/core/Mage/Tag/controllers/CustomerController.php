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

        $this->loadLayout(array('default', 'customer_account'), 'customer_account');

        $this->getLayout()->getBlock('root')->setHeaderTitle(__('My Account'));
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('tag/customer_view'));

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
        $model->loadByTagCustomer($tagId, $customerId);
        if( $model->getCustomerId() == $customerId ) {
            $model->delete();
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
            return;
        } else {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
            return;
        }
    }
}
