<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Constraint;

use Magento\Store\Test\Fixture\Store;
use Mtf\Constraint\AbstractConstraint;
use Magento\Core\Test\Fixture\SystemVariable;
use Magento\Core\Test\Page\Adminhtml\SystemVariableIndex;
use Magento\Core\Test\Page\Adminhtml\SystemVariableNew;

/**
 * Class AssertCustomVariableForm
 */
class AssertCustomVariableForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that data at the form corresponds to the fixture data
     *
     * @param SystemVariable $customVariable
     * @param SystemVariableIndex $systemVariableIndex
     * @param SystemVariableNew $systemVariableNew
     * @param Store $storeOrigin
     * @param SystemVariable $customVariableOrigin
     * @return void
     */
    public function processAssert(
        SystemVariable $customVariable,
        SystemVariableIndex $systemVariableIndex,
        SystemVariableNew $systemVariableNew,
        Store $storeOrigin = null,
        SystemVariable $customVariableOrigin = null
    ) {
        $data = ($customVariableOrigin === null)
            ? $customVariable->getData()
            : array_merge($customVariableOrigin->getData(), $customVariable->getData());

        if ($data['html_value'] == '') {
            $data['html_value'] = $customVariableOrigin->getHtmlValue();
            $data['use_default_value'] = 'Yes';
        }
        $data['plain_value'] = ($data['plain_value'] == '')
            ? $customVariableOrigin->getPlainValue()
            : $data['plain_value'];

        $filter = ['code' => $data['code']];
        $systemVariableIndex->open();
        $systemVariableIndex->getSystemVariableGrid()->searchAndOpen($filter);
        if ($storeOrigin !== null) {
            $systemVariableNew->getFormPageActions()->selectStoreView($storeOrigin->getStoreId());
        }

        $formData = $systemVariableNew->getSystemVariableForm()->getData($customVariable);
        unset($data['variable_id']);

        $diff = $this->verifyData($formData, $data);
        \PHPUnit_Framework_Assert::assertTrue(
            empty($diff),
            implode(' ', $diff)
        );
    }

    /**
     * Check if arrays have equal values
     *
     * @param array $formData
     * @param array $fixtureData
     * @return array
     */
    protected function verifyData(array $formData, array $fixtureData)
    {
        $errorMessage = [];
        foreach ($fixtureData as $key => $value) {
            if ($key == 'conditions') {
                continue;
            }
            if (is_array($value)) {
                $diff = array_diff($value, $formData[$key]);
                $diff = array_merge($diff, array_diff($formData[$key], $value));
                if (!empty($diff)) {
                    $errorMessage[] = "Data in " . $key . " field not equal."
                        . "\nExpected: " . implode(", ", $value)
                        . "\nActual: " . implode(", ", $formData[$key]);
                }
            } else {
                if ($value !== $formData[$key]) {
                    $errorMessage[] = "Data in " . $key . " field not equal."
                        . "\nExpected: " . $value
                        . "\nActual: " . $formData[$key];
                }
            }
        }
        return $errorMessage;
    }

    /**
     * Text success verify Custom Variable
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed Custom Variable data on edit page(backend) equals to passed from fixture.';
    }
}
