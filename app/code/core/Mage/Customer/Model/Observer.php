<?php
/**
 * Customer module oserver
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Observer
{
    /**
     * Before render layout event observer method
     *
     * @return Varien_Event_Observer $observer
     */
    public function beforeRenderLayout($observer)
    {
        $layout = Mage::registry('action')->getLayout();
        $topLinks = $layout->getBlock('top.links');
        if(!$topLinks){
            return false;
        }
        $topLinks->append($layout->createBlock('core/text_list_link', 'top.links.wishlist')
            ->setLink('', 'href="'.Mage::getUrl('wishlist').'"', __('Wishlist'), ''));

        // Add logout link
        $custSession = Mage::getSingleton('customer/session');
        if ($custSession->isLoggedIn()) {
            if ($topLinks) {
                $topLinks->append($layout->createBlock('core/text_list_link', 'top.links.logout')
                    ->setLink('', 'href="'.Mage::getUrl('customer/account/logout').'"', __('Logout'), ''));
            }
            
/*
            $topMenu = $layout->getBlock('top.menu');
            if ($topMenu) {
                $topMenu->insert($layout->createBlock('core/text_tag', 'top.menu.welcome.separator')
                    ->setTagName('span')
                    ->setTagParam('class', 'separator')
                    ->setContents('|'));
                $topMenu->insert($layout->createBlock('core/text_tag', 'top.menu.welcome')
                    ->setTagName('strong')
                    ->setContents(__('Welcome').', ' . $custSession->getCustomer()->getName()));
            }
*/
        }
    }
}
