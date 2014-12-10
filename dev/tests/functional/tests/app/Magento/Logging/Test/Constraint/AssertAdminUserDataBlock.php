<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Logging\Test\Constraint;

use Magento\Logging\Test\Fixture\Logging;
use Magento\Logging\Test\Page\Adminhtml\Details;
use Mtf\Constraint\AbstractConstraint;

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
                    . "\nActual: " . $value;
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
