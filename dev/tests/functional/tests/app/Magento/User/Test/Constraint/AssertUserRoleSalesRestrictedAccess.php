<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Constraint;

use Magento\Backend\Test\Page\Dashboard;
use Magento\User\Test\Page\Adminhtml\UserIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertUserRoleSalesRestrictedAccess
 */
class AssertUserRoleSalesRestrictedAccess extends AbstractConstraint
{
    const ROLE_RESOURCE = 'sales';
    const DENIED_ACCESS = 'Access denied';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that user has only Sales-related permissions
     *
     * @param Dashboard $dashboard
     * @param UserIndex $userIndex
     * @return void
     */
    public function processAssert(
        Dashboard $dashboard,
        UserIndex $userIndex
    ) {
        $menuItems = $dashboard->getMenuBlock()->getTopMenuItems();
        $userIndex->open();
        $deniedMessage = $userIndex->getAccessDeniedBlock()->getTextFromAccessDeniedBlock();
        $hasSales = (in_array(self::ROLE_RESOURCE, $menuItems) && count($menuItems) == 1) ? true : false;
        \PHPUnit_Framework_Assert::assertTrue(
            $hasSales && self::DENIED_ACCESS == $deniedMessage,
            'Sales item is absent in Menu block.'
        );
    }

    /**
     * Returns success message if assert true.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales item is present in Menu block.';
    }
}
