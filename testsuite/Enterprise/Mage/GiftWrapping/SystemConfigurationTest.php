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
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    /**
     * <p>Enabling/Disabling Gift Messages and Gift Wrapping options on Default and Website scope</p>
     * <p>Steps:</p>
     * <p>1. Log into backend;</p>
     * <p>2. Go to Sales page (System Configuration - Sales - Sales - Gift Options);</p>
     * <p>3. Switch to website scope when needed;</p>
     * <p>4. Switch "Allow Gift Messages for Order Items", "Allow Gift Wrapping on Order Level",
     * "Allow Gift Wrapping for Order Items", "Allow Gift Receipt",
     * "Allow Printed Card", "Allow Gift Messages on Order Level" - all to "Yes"/"No".
     * <p>5. Save Configuration.</p>
     * <p>Expected Results:</p>
     * <p>1. Received the message "The configuration has been saved"</p>
     * <p>2. All changed field values are correctly set.</p>
     *
     * @test
     * @param string $settings Name of the dataset with settings
     * @dataProvider changeGiftOptionsSettingsDataProvider
     *
     * @TestlinkId TL-MAGE-829
     * @TestlinkId TL-MAGE-839
     * @TestlinkId TL-MAGE-841
     * @TestlinkId TL-MAGE-843
     */
    public function changeGiftOptionsSettings($settings)
    {
        $settings = $this->loadDataSet('GiftWrapping', $settings);
        $this->systemConfigurationHelper()->configure($settings);
        $this->assertTrue($this->verifyForm($settings['tab_1']['configuration']),
                                            $settings['tab_1']['tab_name']);
    }

    public function changeGiftOptionsSettingsDataProvider()
    {
        return array(
            array('gift_wrapping_all_enable'),
            array('gift_message_all_enable'),
            array('gift_wrapping_all_disable'),
            array('gift_message_all_disable'),
            array('gift_wrapping_all_enable_on_website'),
            array('gift_message_all_enable_on_website'),
            array('gift_wrapping_all_disable_on_website'),
            array('gift_message_all_disable_on_website'),
        );
    }
}
