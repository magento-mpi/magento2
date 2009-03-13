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
 * @package    Enterprise_CustomerBalance
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_CustomerBalance_Model_Observer
{
    public function customerSave($observer)
    {
        $post = Mage::app()->getFrontController()->getAction()->getRequest()->getPost();
        if( isset($post['customerbalance']) ) {
            $data = $post['customerbalance'];

            Mage::getModel('enterprise_customerbalance/balance')
                ->setDelta($data['delta'])
                ->setCustomerId($observer->getEvent()->getCustomer()->getId())
                ->setWebsiteId( $this->_getWebsiteId($observer) )
                ->updateBalance();
        }
    }

    protected function _getWebsiteId($observer)
    {
        if( (bool) Mage::getStoreConfig('customer/account_share/scope') ) {
            return $observer->getEvent()->getCustomer()->getWebsiteId();
        } else {
            $post = Mage::app()->getFrontController()->getAction()->getRequest()->getPost();
            return $post['customerbalance']['website'];
        }
    }
}