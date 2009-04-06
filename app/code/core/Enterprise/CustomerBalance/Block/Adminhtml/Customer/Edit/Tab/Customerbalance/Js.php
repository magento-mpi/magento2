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

class Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Js extends Mage_Adminhtml_Block_Template
{
    public function getCustomerWebsite()
    {
        return Mage::registry('current_customer')->getWebsiteId();
    }

    public function getAllowedWebsitesJson()
    {
        $allowedWebsiteIds = $this->helper('enterprise_permissions')->getAllowedWebsites();
        $websiteCollection = Mage::getModel('core/website')->getCollection()
            ->addIdFilter($allowedWebsiteIds)
            ->load();

        $storeCollection = Mage::getModel('core/store')->getCollection()
            ->addWebsiteFilter($allowedWebsiteIds)
            ->load();

        $dataArray = array();
        foreach( $websiteCollection->getItems() as $item ) {
            $website = Mage::app()->getWebsite($item->getId());

            $dataArray[$item->getId()] = array(
                'name'          => $item->getName(),
                'website_id'    => $item->getId(),
                'currency_code' => $website->getBaseCurrencyCode(),
            );

            $dataArray[$item->getWebsiteId()]['stores'] = array();

            foreach( $storeCollection->getItems() as $store ) {
                if( $store->getWebsiteId() != $item->getId() ) {
                    continue;
                }
                $dataArray[$item->getWebsiteId()]['stores'][$store->getId()] = array(
                    'name'      => $store->getName(),
                    'store_id'  => $store->getId(),
                );
            }
        }

        $json = new Varien_Object();
        $json->setData($dataArray);
        return $json->toJson();
    }
}
