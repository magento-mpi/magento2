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

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Assert that apache redirect correct work.
 */
class AssertRewritesEnabled extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that apache redirect correct work.
     *
     * @param Browser $browser
     * @return void
     */
    public function processAssert(Browser $browser)
    {
        $frontUrl = str_replace('index.php/', '', $_ENV['app_frontend_url']);
        $browser->open($frontUrl . 'index.php/');
        \PHPUnit_Framework_Assert::assertEquals(
            $frontUrl,
            $browser->getUrl(),
            'Apache redirect on front page not work.'
        );

        $browser->open($frontUrl . 'index.php/backend/');
        $isRedirect = strpos($browser->getUrl(), 'index.php') !== false;
        \PHPUnit_Framework_Assert::assertTrue($isRedirect, 'Apache redirect on backend not work.');
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Apache redirect correct work.';
    }
}
