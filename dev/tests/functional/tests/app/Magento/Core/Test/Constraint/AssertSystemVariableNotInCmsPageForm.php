<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Constraint;

use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Magento\Core\Test\Fixture\SystemVariable;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertSystemVariableNotInCmsPageForm
 */
class AssertSystemVariableNotInCmsPageForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that custom system variable not in cms page form
     *
     * @param CmsNew $cmsNewPage
     * @param SystemVariable $systemVariable
     * @return void
     */
    public function processAssert(
        CmsNew $cmsNewPage,
        SystemVariable $systemVariable
    ) {
        $customVariableName = $systemVariable->getName();
        $cmsNewPage->open();
        $cmsPageForm = $cmsNewPage->getPageForm();
        $cmsPageForm->clickInsertVariable();
        $variables = $cmsPageForm->getSystemVariablesBlock()->getAllVariables();

        foreach ($variables as $variable) {
            \PHPUnit_Framework_Assert::assertNotEquals(
                $customVariableName,
                $variable,
                'Custom System Variable "' . $customVariableName . '" is present in Cms Page Form.'
            );
        }
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return "Custom System Variable is absent in Cms Page Form.";
    }
}
