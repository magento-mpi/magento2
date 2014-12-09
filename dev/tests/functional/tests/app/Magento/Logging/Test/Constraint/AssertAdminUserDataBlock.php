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
use Magento\Logging\Test\Fixture\Logging;

/**
 * Class AssertAdminUserDataBlock
 */
class AssertAdminUserDataBlock extends AbstractConstraint
{
    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that Admin User Data block with data according action is presented on page
     *
     * @param Details $pageDetails
     * @param Logging $logging
     * @return void
     */
    public function processAssert(Details $pageDetails, Logging $logging)
    {
        $fixtureData = $logging->getData();
        $formData = $pageDetails->getDetailsBlock()->getData();
        $diff = $this->verifyData($formData, $fixtureData);
        if (!$pageDetails->getDetailsBlock()->isLoggingDetailsGridVisible()) {
            $diff[] = "Logging Details Grid is not present on page.";
        }
        \PHPUnit_Framework_Assert::assertTrue(empty($diff), implode(' ', $diff));
    }

    /**
     * Check if 2 arrays are equal
     *
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
