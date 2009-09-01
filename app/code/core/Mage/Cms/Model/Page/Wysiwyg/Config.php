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
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wysiwyg Config for Editor HTML Element
 *
 * @category    Mage
 * @package     Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_Model_Page_Wysiwyg_Config extends Varien_Object
{
    /**
     * Return Wysiwyg config as Varien_Object
     *
     * @param $data Varien_Object constructor params to override default config values
     * @return Varien_Object
     */
    public function getConfig($data = array())
    {
        $config = new Varien_Object();
        $config->setData(array(
            'enabled'                       => Mage::getStoreConfig('cms/page_wysiwyg/enabled'),
            'files_browser_window_url'      => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_page_wysiwyg_images/index'),
            'files_browser_window_width'    => Mage::getStoreConfig('cms/page_wysiwyg/browser_window_width'),
            'files_browser_window_height'   => Mage::getStoreConfig('cms/page_wysiwyg/browser_window_height'),
            'toggle_link_title'             => Mage::helper('cms')->__('Show/Hide Editor'),
            'encode_directives'             => true,
            'directives_url'                => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_page_wysiwyg/directive'),
            'widget_window_url'             => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_widget/index'),
            'widget_window_no_wysiwyg_url'  => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_widget/index', array('no_wysiwyg' => true)),
            'widget_plugin_src'             => Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/plugins/magentowidget/editor_plugin.js',
            'widget_image_url'              => Mage::getDesign()->getSkinUrl('images/widget_placeholder.gif'),
            'widget_link_text'              => Mage::helper('cms')->__('Insert Widget'),
        ));

        $config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));

        if (is_array($data)) {
            $config->addData($data);
        }

        return $config;
    }


}
