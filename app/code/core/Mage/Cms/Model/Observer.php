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
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * CMS Observer model
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_Model_Observer
{
    /**
     * Modify No Route Forward object
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Cms_Model_Observer
     */
    public function noRoute(Varien_Event_Observer $observer)
    {
        $observer->getEvent()->getStatus()
            ->setLoaded(true)
            ->setForwardModule('cms')
            ->setForwardController('index')
            ->setForwardAction('noRoute');
        return $this;
    }

    /**
     * Modify no Cookies forward object
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Cms_Model_Observer
     */
    public function noCookies(Varien_Event_Observer $observer)
    {
        $redirect = $observer->getEvent()->getRedirect();

        $pageId  = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE);
        $pageUrl = Mage::helper('cms/page')->getPageUrl($pageId);

        if ($pageUrl) {
            $redirect->setRedirectUrl($pageUrl);
        }
        else {
            $redirect->setRedirect(true)
                ->setPath('cms/index/noCookies')
                ->setArguments(array());
        }
        return $this;
    }

    /**
     * Enable WYSIWYG Editor for cms page content if its preconfigured
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Cms_Model_Observer
     */
    public function toggleWysiwygEditor($observer)
    {
        $form = $observer->getEvent()->getForm();
        /* @var $fieldSet Varien_Data_Form_Element_Fieldset */
        $fieldSet = $form->getElement('base_fieldset');
        $editor = $fieldSet->getElements()->searchById('content');
        if (!$editor) {
            return $this;
        }

        $value = $editor->getValue();

        $constructions = array(
            Varien_Filter_Template::CONSTRUCTION_DEPEND_PATTERN,
            Varien_Filter_Template::CONSTRUCTION_IF_PATTERN,
            Varien_Filter_Template::CONSTRUCTION_PATTERN
        );
        $mapping = array();
        foreach ($constructions as $pattern) {
            if (preg_match_all($pattern, $value, $matches, PREG_SET_ORDER)) {
                foreach($matches as $match) {
                    $replacement = '__DIRECTIVE_' . md5($match[0]);
                    $value = str_replace($match[0], $replacement, $value);
                    $mapping[$replacement] = $match[0];
                }
            }
        }

        $editor->setValue($value);

        $enabled = Mage::getStoreConfig('cms/page_wysiwyg/enabled');
        if ($enabled == 'disabled') {
            $editor->setWysiwyg(false);
            return $this;
        }

        $editor->setWysiwyg(true);
        $config = new Varien_Object();
        $config->setData(array(
            'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_page_wysiwyg_images'),
            'files_browser_window_width' => Mage::getStoreConfig('cms/page_wysiwyg/browser_window_width'),
            'files_browser_window_height' => Mage::getStoreConfig('cms/page_wysiwyg/browser_window_height'),
            'toggle_link_title' => Mage::helper('cms')->__('Show/Hide Editor'),
            'mapping' => serialize($mapping)
        ));
        if ($enabled == 'enabled') {
            $config->setEnabled(true);
        }
        $editor->setConfig($config);

        return $this;
    }
}
