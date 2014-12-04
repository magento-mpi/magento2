<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Install\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\Dashboard;
use Mtf\Constraint\AbstractConstraint;

/**
 * Assert that selected currency symbol displays in admin.
 */
class AssertCurrencySelected extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that selected currency symbol displays on dashboard.
     *
     * @param string $currencySymbol
     * @param Dashboard $dashboardPage
     * @return void
     */
    public function processAssert($currencySymbol, Dashboard $dashboardPage)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            strpos($dashboardPage->getMainBlock()->getPrice(), $currencySymbol) !== false,
            'Selected currency symbol not displays on dashboard.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Selected currency displays in admin.';
    }
}
