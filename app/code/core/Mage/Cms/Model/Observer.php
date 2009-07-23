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
     * Enable WYSIWYG Editor for cms page content if its preconfigured and prepare template directives
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Cms_Model_Observer
     */
    public function toggleWysiwygEditor($observer)
    {
        $form = $observer->getEvent()->getForm();
        /* @var $fieldSet Varien_Data_Form_Element_Fieldset */
        $fieldSet = $form->getElement('content_fieldset');
        $editor = $fieldSet->getElements()->searchById('content');
        if (!$editor || !$editor->getValue()) {
            return $this;
        }

        $enabled = Mage::getStoreConfig('cms/page_wysiwyg/enabled');
        if ($enabled == 'disabled') {
            $editor->setWysiwyg(false);
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
                    $replacement = '__DIRECTIVE_' . Mage::helper('core')->urlEncode($match[0]);
                    $value = str_replace($match[0], $replacement, $value);
                    $mapping[$replacement] = $match[0];
                }
            }
        }

        // Replace images URL for displaying them in Wysiwyg
        $imagesSrcRegexp = '/src\s*=\s*[\'\"]{1}(__DIRECTIVE_[a-zA-Z0-9\,\-\_]+)[\'\"]{1}/';
        if (preg_match_all($imagesSrcRegexp, $value, $matches, PREG_SET_ORDER)) {
            $urlModel = Mage::getSingleton('adminhtml/url');
            foreach($matches as $match) {
                $directive = str_replace('__DIRECTIVE_', '', $match[1]);
                $url = $urlModel->getUrl('*/cms_page_wysiwyg_images/image', array('directive' => $directive));
                $mapping[$url] = Mage::helper('core')->urlDecode($directive);
                $value = str_replace($match[1], $url, $value);
            }
        }

        $editor->setValue($value);

        $editor->setWysiwyg(true);
        $config = new Varien_Object();
        $config->setData(array(
            'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_page_wysiwyg_images/index'),
            'files_browser_window_width' => Mage::getStoreConfig('cms/page_wysiwyg/browser_window_width'),
            'files_browser_window_height' => Mage::getStoreConfig('cms/page_wysiwyg/browser_window_height'),
            'toggle_link_title' => Mage::helper('cms')->__('Show/Hide Editor'),
            'directives_mapping' => serialize($mapping)
        ));
        $editor->setConfig($config);

        if ($enabled == 'enabled') {
            $config->setEnabled(true);
        }

        return $this;
    }

    /**
     * Parse page content and replace Wysiwyg directives with their values
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Cms_Model_Observer
     */
    public function prepareWysiwygContent($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $page = $observer->getEvent()->getPage();
        foreach ($request->getPost() as $field => $value) {
            if (preg_match('/_directives_mapping$/', $field)) {
                continue;
            }
            $fieldMapping = $field . '_directives_mapping';
            if ($request->getPost($fieldMapping)) {
                try {
                    $mapping = unserialize($request->getPost($fieldMapping));
                    if (is_array($mapping) && count($mapping) > 0) {
                        $search = array_keys($mapping);
                        $replace = array_values($mapping);
                        $page->setData($field, str_replace($search, $replace, $value));
                        $page->unsetData($fieldMapping);
                    }
                } catch (Exception $e) {
                    continue;
                }
            }
        }
        return $this;
    }
}
