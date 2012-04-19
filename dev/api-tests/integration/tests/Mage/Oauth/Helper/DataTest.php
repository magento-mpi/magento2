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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Oauth
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test OAuth data helper
 */
class Mage_Oauth_Helper_DataTest extends Magento_TestCase
{
    /**
     * OAuth data helper instance
     *
     * @var Mage_Oauth_Helper_Data
     */
    protected $_helper;

    /**
     * Create OAuth data helper instance
     */
    protected function setUp()
    {
        $this->_helper = new Mage_Oauth_Helper_Data;
        parent::setUp();
    }

    /**
     * Test calculation of cleanup possibility for data with lifetime property
     *
     * @magentoConfigFixture current_store oauth/cleanup/cleanup_probability 1
     * @return void
     */
    public function testIsCleanupProbability()
    {
        $this->assertTrue($this->_helper->isCleanupProbability());
    }

    /**
     * Test calculation of cleanup possibility for data with lifetime property (zero config value)
     *
     * @magentoConfigFixture current_store oauth/cleanup/cleanup_probability 0
     * @return void
     */
    public function testIsCleanupProbabilityWithZeroValue()
    {
        $this->assertFalse($this->_helper->isCleanupProbability());
    }

    /**
     * Test calculation of cleanup possibility for data with lifetime property (string config value)
     *
     * @magentoConfigFixture current_store oauth/cleanup/cleanup_probability qwerty
     * @return void
     */
    public function testIsCleanupProbabilityWithStringValue()
    {
        $this->assertFalse($this->_helper->isCleanupProbability());
    }

    /**
     * Test getting cleanup expiration period value from system configuration in minutes
     *
     * @magentoConfigFixture current_store oauth/cleanup/expiration_period 500
     * @return void
     */
    public function testGetCleanupExpirationPeriod()
    {
        $period = $this->_helper->getCleanupExpirationPeriod();

        $this->assertInternalType('int', $period);
        $this->assertEquals(500, $period);
    }

    /**
     * Test getting cleanup expiration period value from system configuration in minutes (zero config value)
     *
     * @magentoConfigFixture current_store oauth/cleanup/expiration_period 0
     * @return void
     */
    public function testGetCleanupExpirationPeriodWithZeroValue()
    {
        $period = $this->_helper->getCleanupExpirationPeriod();

        $this->assertInternalType('int', $period);
        $this->assertEquals(Mage_Oauth_Helper_Data::CLEANUP_EXPIRATION_PERIOD_DEFAULT, $period);
    }

    /**
     * Test getting cleanup expiration period value from system configuration in minutes (string config value)
     *
     * @magentoConfigFixture current_store oauth/cleanup/expiration_period qwerty
     * @return void
     */
    public function testGetCleanupExpirationPeriodWithStringValue()
    {
        $period = $this->_helper->getCleanupExpirationPeriod();

        $this->assertInternalType('int', $period);
        $this->assertEquals(Mage_Oauth_Helper_Data::CLEANUP_EXPIRATION_PERIOD_DEFAULT, $period);
    }
}
