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
     * Skipped fields for verify data
     *
     * @var array
     */
    protected $skippedFields = ['use_default_value', 'variable_id'];

    /**
     * Assert that data at the form corresponds to the fixture data
     *
     * @param SystemVariable $customVariable
     * @param SystemVariableIndex $systemVariableIndex
     * @param SystemVariableNew $systemVariableNew
     * @param Store $storeOrigin
     * @param SystemVariable $customVariableOrigin
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function processAssert(
        SystemVariable $customVariable,
        SystemVariableIndex $systemVariableIndex,
        SystemVariableNew $systemVariableNew,
        Store $storeOrigin = null,
        SystemVariable $customVariableOrigin = null
    ) {
        // Prepare data
        $data = $customVariableOrigin
            ? array_replace_recursive($customVariableOrigin->getData(), $customVariable->getData())
            : $customVariable->getData();

        // Perform assert
        $systemVariableIndex->open();
        $systemVariableIndex->getSystemVariableGrid()->searchAndOpen(['code' => $data['code']]);

        $data = array_diff_key($data, array_flip($this->skippedFields));
        $formData = $systemVariableNew->getSystemVariableForm()->getData($customVariable);
        $errors = $this->verifyData($data, $formData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);

        if ($storeOrigin !== null) {
            $systemVariableNew->getFormPageActions()->selectStoreView($storeOrigin->getName());
            $formData = $systemVariableNew->getSystemVariableForm()->getData($customVariable);
            $errors = $this->verifyData($data, $formData);
            \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
        }
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
