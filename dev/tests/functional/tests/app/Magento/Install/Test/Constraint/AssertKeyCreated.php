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

use Mtf\Constraint\AbstractConstraint;
use Magento\Install\Test\Page\Install;
use Magento\Install\Test\Fixture\Install as InstallConfig;

/**
 * Assert that selected encryption key displays on success full install page.
 */
class AssertKeyCreated extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that selected encryption key displays on success full install page.
     *
     * @param Install $installPage
     * @param InstallConfig $installConfig
     * @return void
     */
    public function processAssert(Install $installPage, InstallConfig $installConfig)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            $installConfig->getKeyValue(),
            $installPage->getInstallBlock()->getAdminInfo()['encryption_key'],
            'Selected encryption key on install page not equals to data from fixture.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Selected encryption key displays on success full install page.';
    }
}
