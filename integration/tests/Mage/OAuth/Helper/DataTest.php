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
 * @package     Mage_OAuth
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test OAuth data helper
 */
class Mage_OAuth_Helper_DataTest extends Magento_TestCase
{
    /**
     * Test calculation of cleanup possibility for data with lifetime property
     *
     * @magentoConfigFixture current_store oauth/cleanup/cleanup_probability 1
     * @return void
     */
    public function testIsCleanupProbability()
    {
        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = new Mage_OAuth_Helper_Data;

        $this->assertTrue($helper->isCleanupProbability());
    }

    /**
     * Test calculation fail of cleanup possibility for data with lifetime property
     *
     * @magentoConfigFixture current_store oauth/cleanup/cleanup_probability 0
     * @return void
     */
    public function testIsCleanupProbabilityFail()
    {
        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = new Mage_OAuth_Helper_Data;

        $this->assertFalse($helper->isCleanupProbability());
    }
}
