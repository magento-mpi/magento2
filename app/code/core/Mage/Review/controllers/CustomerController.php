<?php
/**
 * Customer reviews controller
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Review_CustomerController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout(array('default', 'customer_account'), 'customer_account');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('review/customer_list')
        );

        $this->renderLayout();
    }
}