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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Application configuration renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Configuration extends Mage_Core_Block_Template
{
    protected $_app;

    /**
     * Init current application
     *
     * @return Mage_XmlConnect_Block_Configuration
     */
    protected function _beforeToHtml()
    {
        $app = Mage::registry('current_app');
        if ($app) {
            $this->_app = $app;
        } else {
            $this->_app = Mage::getModel('xmlconnect/application');
            $this->_app->loadDefaultConfiguration();
        }
        return $this;
    }

    /**
     * Recursively build XML configuration tree
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $section
     * @param array $subtree
     * @param string $prefix
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _buildRecursive($section, $subtree)
    {
        foreach ($subtree as $key => $value) {
            if (is_array($value)) {
                if (strtolower(substr($key, -4)) == 'font') {
                    if (empty($value['name']) || empty($value['size']) || empty($value['color'])) {
                        continue;
                    }
                    $font = $section->addChild($key);
                    $font->addAttribute('name', $value['name']);
                    $font->addAttribute('size', $value['size']);
                    $font->addAttribute('color', $value['color']);
                }
                elseif ($key == 'pages') {
                    $subsection = $section->addChild('content');
                    foreach($value as $page) {
                        $this->_buildRecursive($subsection->addChild('page'), $page);
                    }
                }
                else {
                    $subsection = $section->addChild($key);
                    $this->_buildRecursive($subsection, $value);
                }
            }
            elseif ($value instanceof Mage_XmlConnect_Model_Tabs) {
                foreach($value->getRenderTabs() as $tab) {
                    $subsection = $section->addChild('tab');
                    $this->_buildRecursive($subsection, $tab);
                }
            }
            else {
                if (!empty($value)) {
                    if ((substr($key, -4) == 'icon') ||
                        (substr($key, -4) == 'Icon') ||
                        (substr($key, -5) == 'Image')) {
                        $value = Mage::getBaseUrl('media') . 'xmlconnect/' . $value;
                    }
                    $section->addChild($key, $value);
                }
            }
        }
    }

    /**
     * Render block
     *
     * @return string
     */
    protected function _toHtml()
    {
        $xml = new Mage_XmlConnect_Model_Simplexml_Element('<configuration></configuration>');
        $this->_buildRecursive($xml, $this->_app->getRenderConf());

        $xml->addChild('updateTimeUTC', strtotime($this->_app->getUpdatedAt()));

        $storeId = $this->_app->getStoreId();
        $currencyCode = Mage::app()->getStore($storeId)->getBaseCurrencyCode();
        $xml->addChild('currencyCode', $currencyCode);
        $xml->addChild('secureBaseUrl', Mage::getStoreConfig('web/secure/base_url', $storeId));

        $maxRecepients = 0;
        if ( Mage::getStoreConfig('sendfriend/email/enabled') ) {
            $maxRecepients = Mage::getStoreConfig('sendfriend/email/max_recipients');
        }
        $email = $xml->addChild('emailToFriend');
        $email->addChild('maxRecepients', $maxRecepients);

        $xml->addChild('paypalBusinessAccount', Mage::getStoreConfig('paypal/general/business_account'));
        $xml->addChild('paypalMerchantLabel', $this->_app->getData('conf/special/merchantLabel'));

        return $xml->asNiceXml();
    }
}
