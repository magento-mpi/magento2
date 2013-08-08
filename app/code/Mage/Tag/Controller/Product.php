<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tagged products controller
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Controller_Product extends Magento_Core_Controller_Front_Action
{
    public function listAction()
    {
        $tagId = $this->getRequest()->getParam('tagId');
        $tag = Mage::getModel('Mage_Tag_Model_Tag')->load($tagId);

        if(!$tag->getId() || !$tag->isAvailableInStore()) {
            $this->_forward('404');
            return;
        }
        Mage::register('current_tag', $tag);

        $this->loadLayout();
        $this->_initLayoutMessages('Mage_Checkout_Model_Session');
        $this->_initLayoutMessages('Mage_Tag_Model_Session');
        $this->renderLayout();
    }
}
