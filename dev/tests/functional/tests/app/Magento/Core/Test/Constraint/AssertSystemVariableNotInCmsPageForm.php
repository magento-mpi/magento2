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
     */
    public function processAssert(
        CmsNew $cmsNewPage,
        SystemVariable $systemVariable
    ) {
        $customVariableName = $systemVariable->getName();
        $cmsNewPage->open();
        $cmsPageForm = $cmsNewPage->getPageForm();
        $cmsPageForm->clickInsertVariable();
        $systemVariables = $cmsPageForm->getSystemVariablesBlock()->getAllVariables();

        foreach ($systemVariables as $systemVariable) {
            \PHPUnit_Framework_Assert::assertNotEquals(
                $customVariableName,
                $systemVariable,
                'Custom System Variable "' . $customVariableName . '" is present in Cms Page Form.'
            );
        }
    }

    /**
     * Text of custom system variable not in cms page form
     *
     * @return string
     */
    public function toString()
    {
        return "Custom System Variable is absent in Cms Page Form.";
    }
}
