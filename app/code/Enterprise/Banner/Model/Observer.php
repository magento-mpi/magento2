<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Banner observer model
 */
class Enterprise_Banner_Model_Observer
{

    /**
     * Prepare catalog rule post data to save
     *
     * @param Magento_Event_Observer $observer
     * @return Enterprise_Banner_Model_Observer
     */
    public function prepareCatalogRuleSave(Magento_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $request->setPost(
            'related_banners',
            Mage::helper('Magento_Adminhtml_Helper_Js')->decodeGridSerializedInput($request->getPost('related_banners'))
        );
        return $this;
    }

    /**
     * Bind specified banners to catalog rule
     *
     * @param   Magento_Event_Observer $observer
     * @return  Enterprise_Banenr_Model_Observer
     */
    public function bindRelatedBannersToCatalogRule(Magento_Event_Observer $observer)
    {
        $catalogRule = $observer->getEvent()->getRule();
        $resource = Mage::getResourceModel('Enterprise_Banner_Model_Resource_Banner');
        $banners = $catalogRule->getRelatedBanners();
        if (empty($banners)) {
            $banners = array();
        }
        $resource->bindBannersToCatalogRule($catalogRule->getId(), $banners);
        return $this;
    }

    /**
     * Prepare sales rule post data to save
     *
     * @param Magento_Event_Observer $observer
     * @return Enterprise_Banner_Model_Observer
     */
    public function prepareSalesRuleSave(Magento_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $request->setPost(
            'related_banners',
            Mage::helper('Magento_Adminhtml_Helper_Js')->decodeGridSerializedInput($request->getPost('related_banners'))
        );
        return $this;
    }

    /**
     * Bind specified banners to sales rule
     *
     * @param   Magento_Event_Observer $observer
     * @return  Enterprise_Banenr_Model_Observer
     */
    public function bindRelatedBannersToSalesRule(Magento_Event_Observer $observer)
    {
        $salesRule = $observer->getEvent()->getRule();
        $resource = Mage::getResourceModel('Enterprise_Banner_Model_Resource_Banner');
        $banners = $salesRule->getRelatedBanners();
        if (empty($banners)) {
            $banners = array();
        }
        $resource->bindBannersToSalesRule($salesRule->getId(), $banners);
        return $this;
    }
}
