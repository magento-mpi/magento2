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
     * Selenium TestConfiguration instance
     *
     * @var Mage_Selenium_TestConfiguration
     */
    protected $_testConfiguration = null;

    public function __construct()
    {
        parent::__construct();
        $this->_testConfiguration = Mage_Selenium_TestConfiguration::initInstance();
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::init()
     */
    public function testInit()
    {
        $this->assertInstanceOf('Mage_Selenium_TestConfiguration', $this->_testConfiguration->init());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getFileHelper()
     */
    public function testGetFileHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_FileHelper', $this->_testConfiguration->getFileHelper());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getPageHelper()
     */
    public function testGetPageHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_PageHelper', $this->_testConfiguration->getPageHelper());
        $this->assertInstanceOf('Mage_Selenium_PageHelper', $this->_testConfiguration->getPageHelper(new Mage_Selenium_TestCase()));
        $this->assertInstanceOf('Mage_Selenium_PageHelper', $this->_testConfiguration->getPageHelper(null, new Mage_Selenium_SutHelper($this->_testConfiguration)));
        $this->assertInstanceOf('Mage_Selenium_PageHelper', $this->_testConfiguration->getPageHelper(new Mage_Selenium_TestCase(), new Mage_Selenium_SutHelper($this->_testConfiguration)));
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getDataGenerator()
     */
    public function testGetDataGenerator()
    {
        $this->assertInstanceOf('Mage_Selenium_DataGenerator', $this->_testConfiguration->getDataGenerator());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getDataHelper()
     */
    public function testGetDataHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_DataHelper', $this->_testConfiguration->getDataHelper());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getSutHelper()
     */
    public function testGetSutHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_SutHelper', $this->_testConfiguration->getSutHelper());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getUidHelper()
     */
    public function testGetUidHelper()
    {
        $this->assertInstanceOf('Mage_Selenium_Uid', $this->_testConfiguration->getUidHelper());
    }

    /**
     * Testing Mage_Selenium_TestConfiguration::getConfigValue()
     */
    public function testGetConfigValue()
    {
        $this->assertInternalType('array', $this->_testConfiguration->getConfigValue());

        $this->assertFalse($this->_testConfiguration->getConfigValue('invalid-path'));

        $this->assertInternalType('array', $this->_testConfiguration->getConfigValue('browsers'));
        $this->assertArrayHasKey('default', $this->_testConfiguration->getConfigValue('browsers'));

        $this->assertInternalType('array', $this->_testConfiguration->getConfigValue('browsers/default'));
        $this->assertArrayHasKey('browser', $this->_testConfiguration->getConfigValue('browsers/default'));

        $this->assertInternalType('string', $this->_testConfiguration->getConfigValue('browsers/default/browser'));
        $this->assertInternalType('int', $this->_testConfiguration->getConfigValue('browsers/default/port'));
    }

}