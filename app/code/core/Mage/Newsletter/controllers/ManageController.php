<?php
/**
 * Customers newsletter subscription controller
 *
 * @package    Mage
 * @subpackage Customer
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Newsletter_ManageController extends Mage_Core_Controller_Front_Action
{

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $action = $this->getRequest()->getActionName();

       if (!Mage::getSingleton('customer/session')->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
       }
    }

	public function indexAction()
	{
        $this->loadLayout(array('default', 'customer_account'), 'customer_account');

		$this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('customer/newsletter')
        );

		$this->renderLayout();
	}

	public function saveAction()
	{
		try {
			Mage::getSingleton('customer/session')->getCustomer()
				->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
				->save();
			Mage::getSingleton('customer/session')->addSuccess(__('The subscription has been saved successfully'));
		}
		catch (Exception $e) {
			Mage::getSingleton('customer/session')->addError(__('There has been an error while saving your subscription'));
		}

		$this->_redirect('customer/account/');
	}
}// Class Mage_Customer_NewsletterController END