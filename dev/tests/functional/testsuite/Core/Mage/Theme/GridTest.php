<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Theme
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme grid tests for Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Theme_GridTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Bug Cover<p/>
     * <p>Verification of MAGETWO-4638:</p>
     *
     * @test
     */
    public function openGridPage()
    {
        $this->loginAdminUser();
        $this->navigate('theme_list');
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_grid'), 'Theme grid table is not present');
    }

    /**
     * <p>Bug Cover<p/>
     * <p>Verification of MAGETWO-4638:</p>
     *
     * @depends openGridPage
     * @test
     */
    public function openNewThemePage()
    {
        $this->clickButton('add_new_theme');
        $this->assertTrue($this->controlIsPresent('fieldset', 'theme_form'), 'Theme form fieldset is not present');
    }
}
