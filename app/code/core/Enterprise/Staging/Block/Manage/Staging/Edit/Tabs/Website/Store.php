<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Staging website data
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Website_Store extends Mage_Adminhtml_Block_Template
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('enterprise/staging/manage/staging/edit/tabs/website/store.phtml');
    }

    /**
     * Return User stores html
     * 
     * @return string
     */
    public function getUsedStoresHtml()
    {
        $html           = '';

        $staging        = $this->getStaging();

        $stagingWebsite = $this->getStagingWebsite();

        $website        = $this->getWebsite();

        if ($stagingWebsite) {
            foreach ($stagingWebsite->getStoresCollection() as $stagingStore) {
                $store = $stagingStore->getMasterStore();
                $html .= $this->getLayout()
                    ->createBlock('enterprise_staging/manage_staging_edit_tabs_website_store_item')
                    ->setStaging($staging)
                    ->setStagingWebsite($stagingWebsite)
                    ->setWebsite($website)
                    ->setStagingStore($stagingStore)
                    ->setStore($store)
                    ->toHtml();
            }
        }

        return $html;
    }

    /**
     * Return Store collection
     * 
     * @return array
     */
    public function getStoreCollection()
    {
        $website = $this->getWebsite();
        if ($website) {
            return $website->getStoreCollection();
        } else {
            return array();
        }
    }

    /**
     * Return store url
     * 
     * @return string
     */
    public function getCreateStagingStoreUrl()
    {
        $params = array(
            '_current'  => true,
            'id'        => $this->getStaging()->getId(),
            'website'   => $this->getWebsite()->getId()
        );
        if ($this->getStagingWebsite()) {
            $params['staging_website'] = $this->getStagingWebsite()->getId();
        }
        return $this->getUrl('*/*/createStagingStore', $params);
    }

    /**
     * Retrive staging object from setted data if not from registry
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }

    /**
     * Retrive website object from setted data if not from registry
     *
     * @return Mage_Core_Model_Website
     */
    public function getWebsite()
    {
        if (!($this->getData('website') instanceof Mage_Core_Model_Website)) {
            $this->setData('website', Mage::registry('website'));
        }
        return $this->getData('website');
    }

    /**
     * Retrive staging website object from setted data if not from registry
     *
     * @return Mage_Core_Model_Website
     */
    public function getStagingWebsite()
    {
        if (!($this->getData('staging_website') instanceof Enterprise_Staging_Model_Staging_Website)) {
            $this->setData('staging_website', Mage::registry('staging_website'));
        }
        return $this->getData('staging_website');
    }
}
