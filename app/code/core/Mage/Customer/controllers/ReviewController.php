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

class Mage_Customer_ReviewController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        #$this->loadLayout('review/reviews');
        $this->loadLayout();

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('review/customer_list')
                ->setUsePager(true)
        );

        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('review/customer_view')
        );

        $this->renderLayout();
    }
}
