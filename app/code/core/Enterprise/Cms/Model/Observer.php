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
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Enterprise cms page observer
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Model_Observer
{
    /**
     * Configuration model
     * @var Enterprise_Cms_Model_Config
     */
    protected $_config;

    /**
     * Contructor
     */
    public function __construct()
    {
        $this->_config = Mage::getSingleton('enterprise_cms/config');
    }

    /**
     * Making changes to main tab regarding to custom logic
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Cms_Model_Observer
     */
    public function onMainTabPrepareForm($observer)
    {
        $form = $observer->getEvent()->getForm();
        /* @var $baseFieldset Varien_Data_Form_Element_Fieldset */
        $baseFieldset = $form->getElement('base_fieldset');

        /*
         * Making is_active as disabled if user does not have publish permission
         */
        if (!$this->_config->isCurrentUserCanPublishRevision()) {
            $element = $baseFieldset->getElements()->searchById('is_active');
            if ($element) {
                $element->setDisabled(true);
            }
        }

        /*
         * Adding link to current published revision
         */
        /* @var $page Enterprise_Cms_Model_Page */
        $page = Mage::registry('cms_page');
        $revisionAvailable = false;
        if ($page && $page->getPublishedRevisionId()) {
            $revision = Mage::getModel('enterprise_cms/page_revision')
                ->setUserId(Mage::getSingleton('admin/session')->getUser()->getId())
                ->setAccessLevel($this->_config->getAllowedAccessLevel())
                ->load($page->getPublishedRevisionId());

            if ($revision->getId()) {
                $revisionNumber = $revision->getRevisionNumber();
                $versionNumber = $revision->getVersionNumber();
                $versionLabel = $revision->getLabel();

                $afterElementHtml = '';

                if ($versionLabel) {
                    $afterElementHtml .= "\n" .
                        Mage::helper('enterprise_cms')->__('Version Label:') . $versionLabel;
                }

                $page->setPublishedRevisionLink(
                    Mage::helper('enterprise_cms')->__('Published Revision #%s', $revisionNumber));

                $baseFieldset->addType('link', 'Enterprise_Cms_Block_Form_Element_Link');
                $baseFieldset->addField('published_revision_link', 'link', array(
                        'href' => Mage::getModel('adminhtml/url')->getUrl('*/cms_page_revision/edit', array(
                            'page_id' => $page->getId(),
                            'revision_id' => $page->getPublishedRevisionId()
                            )),
                        'after_element_html' => $afterElementHtml
                    ));

                $revisionAvailable = true;
            }
        }

        /*
         * User does not have access to revision or revision is no longer available
         */
        if (!$revisionAvailable && $page->getId()) {
            $baseFieldset->addField('published_revision_status', 'label', array('bold' => true));
            $page->setPublishedRevisionStatus(Mage::helper('enterprise_cms')->__('Published Revision Unavailable'));
        }

        return $this;
    }

    /**
     * Validate and render Cms hierarchy page
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Cms_Model_Observer
     */
    public function cmsControllerRouterMatchBefore(Varien_Event_Observer $observer)
    {
        /* @var $helper Enterprise_Cms_Helper_Hierarchy */
        $helper = Mage::helper('enterprise_cms/hierarchy');
        if (!$helper->isEnabled()) {
            return $this;
        }
        $condition = $observer->getEvent()->getCondition();
        $helper->match($condition);
        return $this;
    }

    /**
     * Processing extra data after cms page saved
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Cms_Model_Observer
     */
    public function cmsPageSaveAfter(Varien_Event_Observer $observer)
    {
        /* @var $page Mage_Cms_Model_Page */
        $page = $observer->getEvent()->getObject();

        if ($page->getIsNewPage()) {
            $revision = Mage::getModel('enterprise_cms/page_revision')
                ->setData($page->getData())
                ->setCopiedFromOriginal(true)
                ->save()
                ->publish();
        }

        if (!Mage::helper('enterprise_cms/hierarchy')->isEnabled()) {
            return $this;
        }
        if (!$page->dataHasChangedFor('identifier')) {
            return $this;
        }

        Mage::getSingleton('enterprise_cms/hierarchy_node')->updateRewriteUrls($page);

        return $this;
    }

    /**
     * Preparing cms page object before it will be saved
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Cms_Model_Observer
     */
    public function cmsPageSaveBefore(Varien_Event_Observer $observer)
    {
        /* @var $page Mage_Cms_Model_Page */
        $page = $observer->getEvent()->getObject();
        /*
         * All new pages created by user without permission to publish
         * should be disabled from the beginning.
         */
        if (!$page->getId()) {
            $page->setIsNewPage(true);
            if (!$this->_config->isCurrentUserCanPublishRevision()) {
                $page->setIsActive(false);
            }
        }

        return $this;
    }

    /**
     * Clean up orphaned private versions.
     *
     * @return Enterprise_Cms_Model_Observer
     */
    public function cleanUpOrphanedPrivateRevisions()
    {
        //Mage::getResourceModel('enterprise_cms/page_version')
        //    ->cleanUpOrphanedRevisions(Enterprise_Cms_Model_Page_Version::ACCESS_LEVEL_PRIVATE);

        /* @var $collection Enterprise_Cms_Model_Mysql4_Page_Version_Collection */
        $collection = Mage::getModel('enterprise_cms/page_version')->getCollection()
            ->addAccessLevelFilter(Enterprise_Cms_Model_Page_Version::ACCESS_LEVEL_PRIVATE)
            ->addUserIdFilter();

        foreach ($collection->getItems() as $item) {
            try {
                $item->delete();
            } catch (Mage_Core_Exception $e) {
                // If we have situation when revision from
                // orphaned private version published we should
                // change its access level to protected so publisher
                // will have chance to see it and assign to some user
                $item->setAccessLevel(Enterprise_Cms_Model_Page_Version::ACCESS_LEVEL_PROTECTED);
                $item->save();
            }
        }

        return $this;
    }
}
