<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterpirse_CustomerBalance
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Controller for Customer account -> Store Credit ajax tab and all its contents
 *
 */
class Enterprise_CustomerBalance_Adminhtml_CustomerbalanceController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Customer balance form
     *
     */
    public function formAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer balance grid
     *
     */
    public function gridHistoryAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('enterprise_customerbalance/adminhtml_customer_edit_tab_customerbalance_balance_history_grid')->toHtml()
        );
    }

    /**
     * Get store views of specified website for transactional email
     *
     */
    public function getAllowedEmailWebsitesAction()
    {
        $websiteId = $this->getRequest()->getParam('website_id');
        if( $websiteId ) {
            $collection = Mage::getModel('core/store')->getCollection()
              ->addWebsiteFilter($websiteId);

            $items = array();
            foreach( $collection->getItems() as $item ) {
                $items[] = $item->getData();
            }

            $items = new Varien_Object($items);
            $this->getResponse()->setBody($items->toJson());
        }
    }

    /**
     * Get currency for specified website
     *
     */
    public function getWebsiteBaseCurrencyAction()
    {
        $websiteId = $this->getRequest()->getParam('website_id');
        if( $websiteId ) {
            $website = Mage::app()->getWebsite($websiteId);
            $this->getResponse()->setBody($website->getBaseCurrencyCode());
        }
    }

    protected function _initCustomer($idFieldName = 'id')
    {
        $customer = Mage::getModel('customer/customer')->load((int)$this->getRequest()->getParam($idFieldName));
        if (!$customer->getId()) {
            Mage::throwException(Mage::helper('enterprise_customerbalance')->__('Failed to initialize customer'));
        }
        Mage::register('current_customer', $customer);
    }
}
