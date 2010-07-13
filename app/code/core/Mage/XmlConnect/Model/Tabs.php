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
class Mage_XmlConnect_Model_Tabs
{
    protected $_enabledTabs;
    protected $_disabledTabs;

    /**
     * Constructor
     */
    public function __construct($data)
    {
        $this->_setDefaultValues();
        if (is_string($data)) {
            $data = json_decode($data);
            if (is_object($data)) {
                $this->_enabledTabs = $data->enabledTabs;
                $this->_disabledTabs = $data->disabledTabs;
            }
        }
    }

    /**
     * Set default values
     */
    protected function _setDefaultValues()
    {
        $this->_enabledTabs = array(
            array(
                'label' => Mage::helper('xmlconnect')->__('Home'),
                'image' => 'tab_home.png',
                'action' => 'Home',
            ),
            array(
                'label' => Mage::helper('xmlconnect')->__('Shop'),
                'image' => 'tab_shop.png',
                'action' => 'Shop',
            ),
            array(
                'label' => Mage::helper('xmlconnect')->__('Search'),
                'image' => 'tab_search.png',
                'action' => 'Search',
            ),
            array(
                'label' => Mage::helper('xmlconnect')->__('Cart'),
                'image' => 'tab_cart.png',
                'action' => 'Cart',
            ),
            array(
                'label' => Mage::helper('xmlconnect')->__('More'),
                'image' => 'tab_more.png',
                'action' => 'More',
            ),
            array(
                'label' => Mage::helper('xmlconnect')->__('Account'),
                'image' => 'tab_account.png',
                'action' => 'Account',
            ),
            array(
                'label' => Mage::helper('xmlconnect')->__('About Us'),
                'image' => 'tab_page.png',
                'action' => 'AboutUs',
            ),
        );
        $this->_disabledTabs = array();
    }

    /**
     *
     */
    public function getEnabledTabs()
    {
        return $this->_enabledTabs;
    }

    /**
     *
     */
    public function getDisabledTabs()
    {
        return $this->_disabledTabs;
    }

    /**
     *
     */
    public function getRenderTabs()
    {
        $result = array();
        foreach ($this->_enabledTabs as $tab) {
            $tab->image = Mage::getDesign()->getSkinUrl('images/xmlconnect/' . $tab->image);
            $result[] = $tab;
        }
        return $result;
    }
}
