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
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Enabling and disabling gift option in system configuration in different scopes (default scope, website scope)
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftWrapping_SystemConfigurationTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to System - System Configuration</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    /**
     * <p>Test Case TL-MAGE-829: Enabling Gift Messages and Gift Wrapping options - Default scope</p>
     * <p>Steps:</p>
     * <p>1. Log into backend;</p>
     * <p>2. Go to Sales page (System Configuration - Sales - Sales - Gift Options);</p>
     * <p>3. Switch "Allow Gift Messages for Order Items" to "Yes";</p>
     * <p>4. Switch "Allow Gift Wrapping on Order Level" to "Yes";</p>
     * <p>5. Switch "Allow Gift Wrapping for Order Items" to "Yes";</p>
     * <p>6. Switch "Allow Gift Receipt" to "Yes";</p>
     * <p>7. Switch "Allow Printed Card" to "Yes";</p>
     * <p>8. Switch "Allow Gift Messages on Order Level" to "Yes";</p>
     * <p>9. Save Configuration.</p>
     * <p>Expected Results:</p>
     * <p>1. Received the message "The configuration has been saved" on successful switching of all mentioned options in steps 3-8;</p>
     * <p>2. All mentioned fields in steps 3-8 switched to "Yes".</p>
     *
     * @test
     */
    public function enableOptionsDefaultScope()
    {
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure('gift_message_all_enable');
    }

    /**
     * <p>Test Case TL-MAGE-839: Disabling Gift Messages and Gift Wrapping options - Default scope</p>
     * <p>Steps:</p>
     * <p>1. Log into backend;</p>
     * <p>2. Go to Sales page (System Configuration - Sales - Sales - Gift Options);</p>
     * <p>3. Switch "Allow Gift Messages for Order Items" to "No";</p>
     * <p>4. Switch "Allow Gift Wrapping on Order Level" to "No";</p>
     * <p>5. Switch "Allow Gift Wrapping for Order Items" to "No";</p>
     * <p>6. Switch "Allow Gift Receipt" to "No";</p>
     * <p>7. Switch "Allow Printed Card" to "No";</p>
     * <p>8. Switch "Allow Gift Messages on Order Level" to "No";</p>
     * <p>9. Save Configuration.</p>
     * <p>Expected Results:</p>
     * <p>1. Received the message "The configuration has been saved" on successful switching of all mentioned options in steps 3-8;</p>
     * <p>2. All mentioned fields in steps 3-8 switched to "No".</p>
     *
     * @test
     */
    public function disableOptionsDefaultScope()
    {
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_disable');
        $this->systemConfigurationHelper()->configure('gift_message_all_disable');
    }

    /**
     * <p>Test Case TL-MAGE-841: Enabling Gift Messages and Gift Wrapping options - Website scope</p>
     * <p>Steps:</p>
     * <p>1. Log into backend;</p>
     * <p>2. Go to Sales page (System Configuration - Sales - Sales - Gift Options);</p>
     * <p>3. Switch to website scope;</p>
     * <p>4. Switch "Allow Gift Messages for Order Items" to "Yes";</p>
     * <p>5. Switch "Allow Gift Wrapping on Order Level" to "Yes";</p>
     * <p>6. Switch "Allow Gift Wrapping for Order Items" to "Yes";</p>
     * <p>7. Switch "Allow Gift Receipt" to "Yes";</p>
     * <p>8. Switch "Allow Printed Card" to "Yes";</p>
     * <p>9. Switch "Allow Gift Messages on Order Level" to "Yes";</p>
     * <p>10. Save Configuration.</p>
     * <p>Expected Results:</p>
     * <p>1. Received the message "The configuration has been saved" on successful switching of all mentioned options in steps 4-9;</p>
     * <p>2. All mentioned fields in steps 4-9 switched to "Yes".</p>
     *
     * @test
     */
    public function enableOptionsWebsiteScope()
    {
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_enable_on_website');
        $this->systemConfigurationHelper()->configure('gift_message_all_enable_on_website');
    }

    /**
     * <p>Test Case TL-MAGE-843: Disabling Gift Messages and Gift Wrapping options - Website scope + Price</p>
     * <p>Steps:</p>
     * <p>1. Log into backend;</p>
     * <p>2. Go to Sales page (System Configuration - Sales - Sales - Gift Options);</p>
     * <p>3. Switch to website scope;</p>
     * <p>4. Switch "Allow Gift Messages for Order Items" to "No";</p>
     * <p>5. Switch "Allow Gift Wrapping on Order Level" to "No";</p>
     * <p>6. Switch "Allow Gift Wrapping for Order Items" to "No";</p>
     * <p>7. Switch "Allow Gift Receipt" to "No";</p>
     * <p>8. Switch "Allow Printed Card" to "No";</p>
     * <p>9. Switch "Allow Gift Messages on Order Level" to "No";</p>
     * <p>10. Enter some price (for example "18.53") in the "Default Price for Printed Card" field;</p>
     * <p>11. Save Configuration.</p>
     * <p>Expected Results:</p>
     * <p>1. Received the message "The configuration has been saved" on successful switching of all mentioned options in steps 4-10;</p>
     * <p>2. All mentioned fields in steps 4-10 switched to "No".</p>
     *
     * @test
     */
    public function disableOptionsWebsiteScope()
    {
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_disable_on_website');
        $this->systemConfigurationHelper()->configure('gift_message_all_disable_on_website');
    }
}
