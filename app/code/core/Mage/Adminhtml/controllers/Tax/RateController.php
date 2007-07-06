<?php
/**
 * Adminhtml tax rates controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Tax_RateController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('catalog');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('catalog'), __('catalog title'), Mage::getUrl('adminhtml/catalog'))
            ->addLink(__('tax'), __('tax title'));

        $this->getLayout()->getBlock('content')->append($block = $this->getLayout()->createBlock('adminhtml/tax_rate', 'tax'));

        $this->renderLayout();
    }
}