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
 * @category    tests
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Unit test for TestConfiguration
 */
class Mage_Selenium_UimapTest extends Mage_PHPUnit_TestCase
{

    /**
     * Test UIMap helper
     */
    public function testUimapHelper()
    {
        $uimapHelper = new Mage_Selenium_Helper_Uimap($this->_config);
        $this->assertNotNull($uimapHelper);

        $uimap = $uimapHelper->getUimap('admin');
        $this->assertNotNull($uimap);
        $this->assertInternalType('array', $uimap);

        $uipage = $uimapHelper->getUimapPage('admin', 'create_customer');
        $this->assertNotNull($uipage);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $uipage = $uimapHelper->getUimapPageByMca('admin', 'customer/new/');
        $this->assertNotNull($uipage);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $uipage = $uimapHelper->getUimapPage('admin', 'wrong_name');
        $this->assertNull($uipage);

        $uipage = $uimapHelper->getUimapPageByMca('admin', 'wrong-path');
        $this->assertNull($uipage);
    }

    /**
     * Test all UIMap classes
     */
    public function testUimapClasses()
    {
        $uipage = $this->getUimapPage('admin', 'create_customer');
        $this->assertNotNull($uipage);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $fieldsets = $uipage->getMainForm()->getAllFieldsets();
        $this->assertNotNull($fieldsets);
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $fieldsets);
        $this->assertGreaterThanOrEqual(1, count($fieldsets));
        $this->assertEquals('fieldsets', $fieldsets->getType());

        $buttons = $uipage->getMainForm()->getAllButtons();
        $this->assertNotNull($buttons);
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
        foreach($buttons as $buttonName => $buttonXPath) {
            $this->assertNotEmpty($buttonXPath);
        }

        $tabs = $uipage->getMainForm()->getTabs();
        $this->assertNotNull($tabs);
        $this->assertInstanceOf('Mage_Selenium_Uimap_TabsCollection', $tabs);
        $this->assertGreaterThanOrEqual(1, count($tabs));

        $tab = $tabs->getTab('addresses');
        $this->assertNotNull($tabs);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Tab', $tab);

        var_dump($uipage->findFieldset('account_info')->getXPath());


        /* Please, don't remove this code for future debugging
        //var_dump($uipage); die;
        var_dump($uipage->getMainForm()->getAllFieldsets());

        var_dump($uipage->getAllButtons());
        var_dump($uipage->getMainForm()->getAllButtons());
        var_dump($uipage->getMainForm()->getTabs()->getTab('addresses'));
        var_dump($uipage->getMainForm()->getTabs()->getTab('addresses')->getAllElements('buttons'));
        var_dump($uipage->getMainForm()->getTab('account_information'));
        var_dump($uipage->getMainForm()->getTab('account_information')->getFieldset('account_info'));
        var_dump($uipage->getMainForm()->getTab('account_information')->getFieldset('password')->getFields());
        var_dump($uipage->getAllButtons()->get('save_customer'));
        var_dump($uipage->getMainForm()->getTab('account_information')->getFieldset('account_info')->getFields()->get('first_name'));
        var_dump($uipage->getMainForm()->getTab('account_information')->getFieldset('account_info')->getAllElements('required'));
        var_dump($uipage->getMainForm()->getTab('account_information')->getFieldset('account_info')->getAllRequired());
        var_dump($uipage->getMessage('success_save_customer'));

        var_dump($uipage->getMainForm()->findField('first_name'));
        var_dump($uipage->getMainForm()->getTab('account_information')->findField('first_name'));
        var_dump($uipage->findMessage('success_save_customer'));
        */
    }

}
