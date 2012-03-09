<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer reviews controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Rss_OrderController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        $user = Mage::helper('Mage_Adminhtml_Helper_Rss')->authAdmin('catalog/reviews_ratings');
        if (!is_object($user)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this;
        }

        return parent::preDispatch();
    }

    public function newAction()
    {
        $user = Mage::helper('Mage_Rss_Helper_Data')->authAdmin('sales/order');
        if (!is_object($user)) {
            return;
        }

        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }
}
