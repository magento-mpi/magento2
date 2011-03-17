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
 * Unit test for SutHelper
 */
class Mage_Selenium_Helper_SutTest extends Mage_PHPUnit_TestCase
{

    /**
     * Selenium SutHelper instance
     *
     * @var Mage_Selenium_Helper_Sut
     */
    protected $_sutHelper = null;

    public function  __construct() {
        parent::__construct();
        $this->_sutHelper = new Mage_Selenium_Helper_Sut($this->_config);
    }

    /**
     * Testing Mage_Selenium_Helper_Sut::setArea()
     */
    public function testSetArea()
    {
        $this->assertInstanceOf('Mage_Selenium_Helper_Sut', $this->_sutHelper->setArea('frontend'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Sut', $this->_sutHelper->setArea('admin'));
    }

    /**
     * Testing OutOfRangeException throwing in Mage_Selenium_Helper_Sut::setArea()
     */
    public function testSetAreaOutOfRangeException()
    {
        $this->setExpectedException('OutOfRangeException');
        $this->_sutHelper->setArea('invalid-area');
    }

    /**
     * Testing Mage_Selenium_Helper_Sut::getArea()
     */
    public function testGetArea()
    {
        $this->_sutHelper->setArea('frontend');

        $this->assertInternalType('string', $this->_sutHelper->getArea());
        $this->assertNotEmpty($this->_sutHelper->getArea());
        $this->assertEquals('frontend', $this->_sutHelper->getArea());
    }

    /**
     * Testing Mage_Selenium_Helper_Sut::getBaseUrl()
     */
    public function testGetBaseUrl()
    {
        $this->assertInternalType('string', $this->_sutHelper->getBaseUrl());
        $this->assertNotEmpty($this->_sutHelper->getBaseUrl());

        $this->_sutHelper->setArea('admin');
        $this->assertRegExp('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/', $this->_sutHelper->getBaseUrl());

        $this->_sutHelper->setArea('frontend');
        $this->assertRegExp('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/', $this->_sutHelper->getBaseUrl());
    }

    /**
     * Testing Mage_Selenium_Helper_Sut::isAdmin()
     */
    public function testIsAdmin()
    {
        $this->_sutHelper->setArea('frontend');
        $this->assertFalse($this->_sutHelper->isAdmin());

        $this->_sutHelper->setArea('admin');
        $this->assertTrue($this->_sutHelper->isAdmin());
    }

    /**
     * Testing Mage_Selenium_Helper_Sut::getDefaultAdminUsername()
     */
    public function testGetDefaultAdminUsername()
    {
        $this->assertInternalType('string', $this->_sutHelper->getDefaultAdminUsername());
        $this->assertNotEmpty($this->_sutHelper->getDefaultAdminUsername());
    }

    /**
     * Testing Mage_Selenium_Helper_Sut::getDefaultAdminPassword()
     */
    public function testGetDefaultAdminPassword()
    {
        $this->assertInternalType('string', $this->_sutHelper->getDefaultAdminPassword());
        $this->assertNotEmpty($this->_sutHelper->getDefaultAdminPassword());
    }
}
?>
