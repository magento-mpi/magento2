<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Core\Test\Fixture\SystemVariable;
use Magento\Cms\Test\Page\Adminhtml\CmsBlockEdit;
use Magento\Cms\Test\Page\Adminhtml\CmsBlockIndex;

/**
 * Class AssertSystemVariableInFooter
 * Assert that Custom Variable is displayed on frontend in footer and has correct data according to dataset
 */
class AssertSystemVariableInFooter extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Add created variable to existed footer block and assert that Custom Variable is displayed on frontend in footer
     * and has correct data according to dataset
     *
     * @param SystemVariable $customVariable
     * @param CmsBlockIndex $blockIndex
     * @param CmsBlockEdit $blockEdit
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(
        SystemVariable $customVariable,
        CmsBlockIndex $blockIndex,
        CmsBlockEdit $blockEdit,
        CmsIndex $cmsIndex
    ) {
        $filter = ['identifier' => 'footer_links'];
        $blockIndex->open();
        $blockIndex->getGrid()->searchAndOpen($filter);

        $blockEdit->getBlockForm()->toggleEditor();
        $blockEdit->getBlockForm()->clickInsertVariable();
        $customVariableName = $customVariable->getName();
        $blockEdit->getBlockForm()->getWysiwygConfig()->selectVariableByName($customVariableName);
        $blockEdit->getPageMainActions()->save();

        $cmsIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsIndex->getFooterBlock()->checkVariable($customVariableName),
            'Custom Variable with name \'' . $customVariableName . '\' is not displayed on frontend on Home page.'
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Custom Variable is displayed on frontend in footer.';
    }
}
