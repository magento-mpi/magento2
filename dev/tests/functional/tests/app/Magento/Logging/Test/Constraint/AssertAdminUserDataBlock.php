<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Logging\Test\Constraint; 

use Mtf\Constraint\AbstractConstraint;
use Magento\Logging\Test\Page\Adminhtml\Details;
use Magento\User\Test\Fixture\AdminUserInjectable;

/**
 * Class AssertAdminUserDataBlock
 */
class AssertAdminUserDataBlock extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * @param Details $details
     * @param AdminUserInjectable $adminUser
     */
    public function processAssert(Details $details, AdminUserInjectable $adminUser)
    {
        $fixtureData = $adminUser->getData();
        $fixtureData['aggregated_information'] = 'general';
        $formData = $details->getDetailsBlock()->getData();
        $diff = $this->verifyData($formData, $fixtureData);
        if (!$details->getDetailsBlock()->isLoggingDetailsGridVisible()) {
            $diff[] = "Logging Details Grid is not present on page.";
        }
        \PHPUnit_Framework_Assert::assertTrue(empty($diff), implode(' ', $diff));
    }

    /**
     * @param array $formData
     * @param array $fixtureData
     * @return array
     */
    protected function verifyData(array $formData, array $fixtureData)
    {
        $errorMessages = [];
        $result = array_diff_assoc($formData, $fixtureData);
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $errorMessages[] = "Data in " . $key . " field is not equal.\nExpected: " . $fixtureData[$key]
                    . "\nActual: " . $value ;
            }
        }
        return $errorMessages;
    }

    /**
     * Text success verify admin logging details
     *
     * @return string
     */
    public function toString()
    {
        return "Displayed admin logging details equal to passed from fixture.";
    }
}
