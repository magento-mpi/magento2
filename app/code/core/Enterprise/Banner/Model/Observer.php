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
 * @category   Mage
 * @package    Enterprise_Banner
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Banner observer model
 */
class Enterprise_Banner_Model_Observer
{

    /**
     * Prepare catalog rule post data to save
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Banner_Model_Observer
     */
    public function prepareCatalogRuleSave(Varien_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $request->setPost('related_banners', Mage::helper('adminhtml/js')->decodeInput($request->getPost('related_banners')));
        return $this;
    }

    /**
     * Bind specified banners to catalog rule
     *
     * @param   Varien_Event_Observer $observer
     * @return  Enterprise_Banenr_Model_Observer
     */
    public function bindRelatedBannersToCatalogRule(Varien_Event_Observer $observer)
    {
        $catalogRule = $observer->getEvent()->getRule();
        $resource = Mage::getResourceModel('enterprise_banner/banner');
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
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Banner_Model_Observer
     */
    public function prepareSalesRuleSave(Varien_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $request->setPost('related_banners', Mage::helper('adminhtml/js')->decodeInput($request->getPost('related_banners')));
        return $this;
    }

    /**
     * Bind specified banners to sales rule
     *
     * @param   Varien_Event_Observer $observer
     * @return  Enterprise_Banenr_Model_Observer
     */
    public function bindRelatedBannersToSalesRule(Varien_Event_Observer $observer)
    {
        $salesRule = $observer->getEvent()->getRule();
        $resource = Mage::getResourceModel('enterprise_banner/banner');
        $banners = $salesRule->getRelatedBanners();
        if (empty($banners)) {
        	$banners = array();
        }
        $resource->bindBannersToSalesRule($salesRule->getId(), $banners);
        return $this;
    }
}
