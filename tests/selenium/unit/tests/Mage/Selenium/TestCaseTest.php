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
 * Unit test for Mage_Selenium_Uid helper
 */
class Mage_Selenium_TestCaseTest extends Mage_PHPUnit_TestCase
{
    public function  __construct() {
        parent::__construct();

        //var_dump($this->_config);
        //die;
    }

    /**
     * Testing Mage_Selenium_TestCase::fillForm()
     */
    public function test_fillForm()
    {
        $_testCaseInst = new Mage_Selenium_TestCase();
        $this->assertNotNull($_testCaseInst);

        $this->assertNotNull($_testCaseInst->loginAdminUser());
        $this->assertNotNull($_testCaseInst->admin('dashboard'));
        $this->assertNotNull($_testCaseInst->navigate('manage_customers'));
        $this->assertEquals($this->_config->getUimapValue($_testCaseInst->getArea(), $_testCaseInst->getCurrentPage().'/title'), $_testCaseInst->getTitle());

        $this->assertNotNull($_testCaseInst->clickButton('add_new_customer'));
        $this->assertEquals($this->_config->getUimapValue($_testCaseInst->getArea(), $_testCaseInst->getCurrentPage().'/title'), $_testCaseInst->getTitle());

        $_formData = $_testCaseInst->loadData('all_fields_customer_account');
        $this->assertNotEmpty($_formData);
        $this->assertInternalType('array', $_formData);

        $this->assertNotNull($_testCaseInst->fillForm($_formData));
    }

}
