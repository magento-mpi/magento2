<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tagged products controller
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Tag_Controller_Product extends Magento_Core_Controller_Front_Action
{
    public function listAction()
    {
        $tagId = $this->getRequest()->getParam('tagId');
        $tag = Mage::getModel('Magento_Tag_Model_Tag')->load($tagId);

        if(!$tag->getId() || !$tag->isAvailableInStore()) {
            $this->_forward('404');
            return;
        }
        Mage::register('current_tag', $tag);

        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Checkout_Model_Session');
        $this->_initLayoutMessages('Magento_Tag_Model_Session');
        $this->renderLayout();
    }
}
