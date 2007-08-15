<?php
/**
 * Customer tags controller
 *
 * @package     Mage
 * @subpackage  Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Tag_CustomerController extends Mage_Core_Controller_Varien_Action
{

    public function indexAction()
    {
        $this->loadLayout(array('default', 'customer_account'), 'customer_account');

        $this->_initLayoutMessages('customer/session');
        #$this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('tag/customer'));
        $this->getLayout()->getBlock('root')->setHeaderTitle(__('My Account'));

        $this->renderLayout();
    }
}
