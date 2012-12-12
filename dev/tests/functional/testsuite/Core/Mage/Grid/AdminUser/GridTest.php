<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftRegistry
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Registry creation into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Grid_AdminUser_GridTest extends Mage_Selenium_TestCase
{
    /**
     *
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Post conditions:</p>
     * <p>Log out from Backend.</p>
     */
    protected function tearDownAfterTestClass()
    {
        $this->logoutAdminUser();
    }

    /**
     * Need to verify that all elements is presented on invitation report_invitations_customers page
     * @test
     * @dataProvider uiElementsTestDataProvider
     *
     */
    public function uiElementsTest($pageName)
    {
        $this->navigate($pageName);
        $page = $this->loadDataSet('Grid', 'grid');
        foreach ($page[$pageName] as $control => $type) {
            foreach ($type as $typeName => $name) {
                if (!$this->controlIsPresent($control, $typeName)) {
                    $this->addVerificationMessage("The $control $typeName is not present on page $pageName");
                }
            }

        }
        $this->assertEmptyVerificationErrors();
    }

    public function uiElementsTestDataProvider()
    {
        return array(array('manage_admin_users')

        );
    }
}