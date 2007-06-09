<?php
/**
 * Currency controller
 *
 * @package     Mage
 * @subpackage  Directory
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_CurrencyController extends Mage_Core_Controller_Front_Action
{
    public function switchAction()
    {
        if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
            $this->getResponse()->setRedirect($referer);
        }
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('core/template', 'currency.switch')
            ->setTemplate('directory/currency/switch.phtml')
            ->assign('currency', Mage::getSingleton('core', 'website')->getCurrentCurrency());
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }
}