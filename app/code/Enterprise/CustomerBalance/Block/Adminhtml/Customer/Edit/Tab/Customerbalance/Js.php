<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Js extends Magento_Adminhtml_Block_Template
{
    public function __construct(Magento_Backend_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
    }

    public function getCustomerWebsite()
    {
        return Mage::registry('current_customer')->getWebsiteId();
    }

    public function getWebsitesJson()
    {
        $result = array();
        foreach (Mage::app()->getWebsites() as $websiteId => $website) {
            $result[$websiteId] = array(
                'name'          => $website->getName(),
                'website_id'    => $websiteId,
                'currency_code' => $website->getBaseCurrencyCode(),
                'groups'        => array()
            );

            foreach ($website->getGroups() as $groupId => $group) {
                $result[$websiteId]['groups'][$groupId] = array(
                    'name' => $group->getName()
                );

                foreach ($group->getStores() as $storeId => $store) {
                    $result[$websiteId]['groups'][$groupId]['stores'][] = array(
                        'name'     => $store->getName(),
                        'store_id' => $storeId,
                    );
                }
            }
        }

        return $this->_coreData->jsonEncode($result);
    }
}
