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
class Enterprise2_Mage_Grid_Report_GridTest extends Mage_Selenium_TestCase
{
    /**
     *
     */
    public function setUpBeforeTests(){
        $this->loginAdminUser();
    }

    /**
     *
     */
    public  function assertPostConditions(){
        $this->logoutAdminUser();
    }

    /**
     * Need to verify that all elements is presented on invitation report_invitations_customers page
     * @test
     * @dataProvider uiElementsTestDataProvider
     * 2
     */
    public function uiElementsTest($pageName)
    {
        $this->navigate($pageName);
        $page = $this->loadDataSet('Report', 'grid');
        foreach ($page[$pageName] as $control=> $type) {
            foreach ($type as $typeName=> $name) {
                if (!$this->controlIsPresent($control, $typeName)) {
                    $this->addVerificationMessage("The $control $typeName is not present on page $pageName");
                }
            }

        }
        $this->assertEmptyVerificationErrors();
    }


public function uiElementsTestDataProvider()
{
    return array(array('invitations_customers'));
}
}