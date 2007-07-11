<?php
/**
 * Adminhtml newsletter queue controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Newsletter_QueueController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Queue list action
     */
    public function indexAction()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        
        $this->loadLayout('baseframe');

        $this->_setActiveMenu('newsletter/queue');
        
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/newsletter_queue', 'queue')
        );

        $this->_addBreadcrumb(__('Newsletter'), __('newsletter title'), Mage::getUrl('adminhtml/newsletter'));
        $this->_addBreadcrumb(__('Newsletter queue'), __('Newsletter queue title'));

        $this->renderLayout();
    }

    /**
     * Queue list Ajax action
     */
    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/newsletter_queue_grid')->toHtml());
    }

}