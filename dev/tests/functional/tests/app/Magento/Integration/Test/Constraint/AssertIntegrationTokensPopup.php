<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Integration\Test\Page\Adminhtml\IntegrationIndex;

/**
 * Class AssertIntegrationTokensPopup
 * Assert that pop-up with tokens is shown after clicking on "Allow" button on Resources popup.
 */
class AssertIntegrationTokensPopup extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Fields to be checked
     *
     * @var array
     */
    protected $fields = [
        'key',
        'consumer_secret',
        'token',
        'token_secret',
    ];

    /**
     * Assert that pop-up with tokens is shown after clicking on "Allow" button on Resources popup.
     * It contains:
     * - Consumer Key;
     * - Consumer Secret;
     * - Access Token;
     * - Access Token Secret;
     * All fields contain generated values.
     *
     * @param IntegrationIndex $integrationIndex
     * @return void
     */
    public function processAssert(IntegrationIndex $integrationIndex)
    {
        $errors = [];
        $tokensData = $integrationIndex->getIntegrationGrid()->getTokensPopup()->getData();
        $tokensKeys = array_keys($tokensData);
        $diff = array_diff($this->fields, $tokensKeys);
        if (!empty($diff)) {
            $errors[] = 'Field(s) "' . implode(', ', $diff) . '" is absent in integration tokens.';
        }
        foreach ($tokensData as $key => $value) {
            if (empty($value)) {
                $errors[] = 'Field with key: ' . $key . '" is empty in integration tokens.';
            }
        }
        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            "Integration tokens is not correct.\nLog:\n" . implode(";\n", $errors)
        );
        $integrationIndex->getIntegrationGrid()->getTokensPopup()->clickDoneButton();
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Tokens is shown and not empty.';
    }
}
