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

                $afterElementHtml = Mage::helper('enterprise_cms')->__('Version #%s', $versionNumber);

                if ($versionLabel) {
                    $afterElementHtml .= "\n" .
                        Mage::helper('enterprise_cms')->__('Version Label:') . $versionLabel;
                }

                $page->setPublishedRevisionLink(
                    Mage::helper('enterprise_cms')->__('Published Revision #%s', $revisionNumber));

                $baseFieldset->addType('link', 'Enterprise_Cms_Block_Form_Element_Link');
                $baseFieldset->addField('published_revision_link', 'link', array(
                        'href' => Mage::getUrl('*/cms_page_revision/edit', array(
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
        if (!$revisionAvailable) {
            $baseFieldset->addField('published_revision_status', 'label', array('bold' => true));
            $page->setPublishedRevisionStatus(Mage::helper('enterprise_cms')->__('Published Revision Unavailable'));
        }

        return $this;
    }

    /**
     * Preparing cms page object before it will be saved
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Cms_Model_Observer
     */
    public function cmsPageBeforeSave($observer)
    {
        $page = $observer->getEvent()->getObject();
        /*
         * All new pages created by yser without permission to publish
         * should be disabled from the begining.
         */
        if (!$page->getId() && !$this->_config->isCurrentUserCanPublishRevision()) {
            $page->setIsActive(false);
        }
    }

    /**
     * Processing extra data after cms page saved
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Cms_Model_Observer
     */
    public function cmsPageAfterSave($observer)
    {
        $page = $observer->getEvent()->getObject();

        if (!$this->getOrigData($this->getIdFieldName())) {
            $revision = Mage::getModel('enterprise_cms/page_revision')
                ->setData($this->getData())
                ->save();
        }
    }
}
