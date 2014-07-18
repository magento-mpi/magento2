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
 * Class AssertSystemVariableForm
 * Check that data at the form corresponds to the fixture data
 */
class AssertSystemVariableForm extends AbstractAssertForm
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
        $dataOrigin = $data;
        if ($data['html_value'] == '') {
            $data['html_value'] = $customVariableOrigin->getHtmlValue();
            $data['use_default_value'] = 'Yes';
        }
        $data['plain_value'] = ($data['plain_value'] == '')
            ? $customVariableOrigin->getPlainValue()
            : $data['plain_value'];

        $filter = ['code' => $data['code']];
        $this->openVariable($systemVariableIndex, $filter);

        if ($storeOrigin !== null) {
            $systemVariableNew->getFormPageActions()->selectStoreView($storeOrigin->getName());
            $diff = $this->getDataFromForm($systemVariableNew, $customVariable, $data, false);
            $this->checkForm($diff);
        }

        if ($customVariableOrigin !== null) {
            $data['html_value'] = $customVariableOrigin->getHtmlValue();
            $data['plain_value'] = $customVariableOrigin->getPlainValue();
            $this->openVariable($systemVariableIndex, $filter);
            $diff = $this->getDataFromForm($systemVariableNew, $customVariable, $data, true);
            $this->checkForm($diff);
        }

        if ($storeOrigin == null && $customVariableOrigin == null) {
            $this->openVariable($systemVariableIndex, $filter);
            $diff = $this->getDataFromForm($systemVariableNew, $customVariable, $dataOrigin, false);
            $this->checkForm($diff);
        }
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
            'Variable data at the form corresponds to the fixture data.'
            . "\nLog:\n" . implode(";\n", $diff)
        );
    }

    /**
     * Get data from variable form
     *
     * @param SystemVariableNew $systemVariableNew
     * @param SystemVariable $customVariable
     * @param array $data
     * @param bool $useDefaultValue
     * @return array
     */

    protected function getDataFromForm(
        SystemVariableNew $systemVariableNew,
        SystemVariable $customVariable,
        $data,
        $useDefaultValue
    ) {
        $formData = $systemVariableNew->getSystemVariableForm()->getData($customVariable);
        unset($data['variable_id']);
        if ($useDefaultValue) {
            unset($data['use_default_value']);
        }

        return $this->verifyData($formData, $data, false, false);
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
