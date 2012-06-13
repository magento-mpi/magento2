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
 * Admin Base Url functionality tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_AdminBaseUrl_AdminBaseURL extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Configuration</p>
     */
    protected function assertPreConditions() // Preconditions
    {
        $this->loginAdminUser(); // Log-in
        $this->navigate('system_configuration'); // Navigate to System -> Configuration
    }

     /**
     * <p>Custom Admin URL is equal to Base URL</p>
     * <p>Steps:</p>
     * <p>1. Log in to  backend;</p>
     * <p>2. Go to System Configuration;</p>
     * <p>3. Open Admin - Admin Base URL fieldset;</p>
     * <p>4. Set Use Custom Admin URL to Yes;</p>
     * <p>5. Enter URL that is equal to Base URL to Custom Admin URL field;</p> 
     * <p>6. Save configuration;</p> 
     * <p>Expected result:</p>
     * <p> Configuration is saved without errors. Admin is reloaded using specified Custom Admin URL. All internal
     * Admin links are using Custom Admin URL. Actually in this case no visual changes should be introduced.;</p>
     *
     *
     * @test
     * @TestlinkId    TL-MAGE-4667
     */
    public function customAdminUrlIsEqual()
    {
        //Data
        $CustomUrlData = $this->loadDataSet('AdminBaseURL', 'admin_custom_url_is_equal');
        $this->addParameter('customUrl', '1');
        //Steps
        $this->systemConfigurationHelper()->configure($CustomUrlData);
        //Verifying
        $this->assertTrue($this->verifyForm($CustomUrlData, 'advanced_admin', array('use_custom_admin_path')),
            'Unexpected value in field');
    }
}
?>