<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Constraint;

use Magento\Store\Test\Fixture\Store;
use Mtf\Constraint\AbstractAssertForm;
use Magento\Core\Test\Fixture\SystemVariable;
use Magento\Core\Test\Page\Adminhtml\SystemVariableIndex;
use Magento\Core\Test\Page\Adminhtml\SystemVariableNew;

/**
 * Class AssertCustomVariableForm
 * Check that data at the form corresponds to the fixture data
 */
class AssertCustomVariableForm extends AbstractAssertForm
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
     * @param SystemVariable $systemVariable
     * @param SystemVariableIndex $systemVariableIndex
     * @param SystemVariableNew $systemVariableNew
     * @param Store $storeOrigin
     * @param SystemVariable $systemVariableOrigin
     * @return void
     */
    public function processAssert(
        SystemVariable $systemVariable,
        SystemVariableIndex $systemVariableIndex,
        SystemVariableNew $systemVariableNew,
        Store $storeOrigin = null,
        SystemVariable $systemVariableOrigin = null
    ) {
        $data = ($systemVariableOrigin === null)
            ? $systemVariable->getData()
            : array_merge($systemVariableOrigin->getData(), $systemVariable->getData());

        if ($data['html_value'] == '') {
            $data['html_value'] = $systemVariableOrigin->getHtmlValue();
            $data['use_default_value'] = 'Yes';
        }
        $data['plain_value'] = ($data['plain_value'] == '')
            ? $systemVariableOrigin->getPlainValue()
            : $data['plain_value'];

        $filter = ['code' => $data['code']];
        $this->openVariable($systemVariableIndex, $filter);

        if ($storeOrigin !== null) {
            $systemVariableNew->getFormPageActions()->selectStoreView($storeOrigin->getStoreId());
            $diff = $this->getDataFromForm($systemVariableNew, $systemVariable, $data, false);
            $this->checkForm($diff);
        }

        if ($systemVariableOrigin !== null) {
            $data['html_value'] = $systemVariableOrigin->getHtmlValue();
            $data['plain_value'] = $systemVariableOrigin->getPlainValue();
            $this->openVariable($systemVariableIndex, $filter);
            $diff = $this->getDataFromForm($systemVariableNew, $systemVariable, $data, true);
            $this->checkForm($diff);
        }
    }

    /**
     * Check if arrays have equal values
     *
     * @param array $formData
     * @param array $fixtureData
     * @param bool $isStrict
     * @param bool $isPrepareError
     * @return array
     */
    protected function verifyData(array $formData, array $fixtureData, $isStrict = false, $isPrepareError = true)
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
     * Open variable on Backend
     *
     * @param SystemVariableIndex $systemVariableIndex
     * @param array $filter
     * @return void
     */
    protected function openVariable(SystemVariableIndex $systemVariableIndex, $filter)
    {
        $systemVariableIndex->open();
        $systemVariableIndex->getSystemVariableGrid()->searchAndOpen($filter);
    }

    /**
     * Check variable Form
     *
     * @param array $diff
     * @return void
     */
    protected function checkForm($diff)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            empty($diff),
            implode(' ', $diff)
        );
    }

    /**
     * Get data from variable form
     *
     * @param SystemVariableNew $systemVariableNew
     * @param SystemVariable $systemVariable
     * @param array $data
     * @param bool $useDefaultValue
     * @return array
     */

    protected function getDataFromForm(
        SystemVariableNew $systemVariableNew,
        SystemVariable $systemVariable,
        $data,
        $useDefaultValue
    ) {
        $formData = $systemVariableNew->getSystemVariableForm()->getData($systemVariable);
        unset($data['variable_id']);
        if ($useDefaultValue) {
            unset($data['use_default_value']);
        }

        return $this->verifyData($formData, $data);
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
