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
class Mage_Selenium_TestConfigurationTest extends Mage_PHPUnit_TestCase
{

    /**
     * Testing Mage_Selenium_TestConfiguration::init()
     */
    public function testInit()
    {
        $this->assertInstanceOf('Mage_Selenium_TestConfiguration', $this->_config->init());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getFileHelper()
     */
    public function testGetFileHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_FileHelper', $this->_config->getFileHelper());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getPageHelper()
     */
    public function testGetPageHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_PageHelper', $this->_config->getPageHelper());
        $this->assertInstanceOf('Mage_Selenium_PageHelper', $this->_config->getPageHelper(new Mage_Selenium_TestCase()));
        $this->assertInstanceOf('Mage_Selenium_PageHelper', $this->_config->getPageHelper(null, new Mage_Selenium_SutHelper($this->_config)));
        $this->assertInstanceOf('Mage_Selenium_PageHelper', $this->_config->getPageHelper(new Mage_Selenium_TestCase(), new Mage_Selenium_SutHelper($this->_config)));
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getDataGenerator()
     */
    public function testGetDataGenerator()
    {
        $this->assertInstanceOf('Mage_Selenium_DataGenerator', $this->_config->getDataGenerator());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getDataHelper()
     */
    public function testGetDataHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_DataHelper', $this->_config->getDataHelper());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getSutHelper()
     */
    public function testGetSutHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_SutHelper', $this->_config->getSutHelper());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getUidHelper()
     */
    public function testGetUidHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_Uid', $this->_config->getUidHelper());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getConfigValue()
     */
    public function testGetConfigValue()
    {
        $this->assertInternalType('array', $this->_config->getConfigValue());
        $this->assertNotEmpty($this->_config->getConfigValue());

        $this->assertFalse($this->_config->getConfigValue('invalid-path'));

        $this->assertInternalType('array', $this->_config->getConfigValue('browsers'));
        $this->assertArrayHasKey('default', $this->_config->getConfigValue('browsers'));

        $this->assertInternalType('array', $this->_config->getConfigValue('browsers/default'));
        $this->assertArrayHasKey('browser', $this->_config->getConfigValue('browsers/default'));

        $this->assertInternalType('string', $this->_config->getConfigValue('browsers/default/browser'));
        $this->assertInternalType('int', $this->_config->getConfigValue('browsers/default/port'));
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getDataValue()
     */
    public function testGetDataValue()
    {
        $this->assertInternalType('array', $this->_config->getDataValue());
        $this->assertNotEmpty($this->_config->getDataValue());

        $this->assertFalse($this->_config->getDataValue('invalid-path'));

        $this->assertArrayHasKey('generic_admin_user', $this->_config->getDataValue());
        $this->assertInternalType('array',  $this->_config->getDataValue('generic_admin_user'));
        $this->assertInternalType('string', $this->_config->getDataValue('generic_admin_user/user_name'));
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getUimapValue()
     */
    public function testGetUimapValue()
    {
        $this->assertInternalType('array', $this->_config->getUimapValue('frontend'));
        $this->assertNotEmpty($this->_config->getUimapValue('frontend'));

        $this->assertInternalType('array', $this->_config->getUimapValue('admin'));
        $this->assertNotEmpty($this->_config->getUimapValue('admin'));

        $this->assertFalse($this->_config->getUimapValue('frontend', 'invalid-path'));
        $this->assertFalse($this->_config->getUimapValue('admin', 'invalid-path'));

        $this->assertInternalType('string', $this->_config->getUimapValue('admin', 'manage_users/mca'));
        $this->assertInternalType('string', $this->_config->getUimapValue('frontend', 'customer_account/mca'));
    }

    /**
     * Testing exception throwing in Mage_Selenium_TestConfiguration::getUimapValue()
     */
    public function testGetUimapValueOutOfRangeException()
    {
        $this->setExpectedException('OutOfRangeException');
        $this->_config->getUimapValue('invalid-area');
    }

}