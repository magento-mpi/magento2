<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Assert that apache redirect correct work.
 */
class AssertRewritesEnabled extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

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
            'Apache redirect on front page does not work.'
        );

        $browser->open($frontUrl . 'index.php/backend/');
        $isRedirect = strpos($browser->getUrl(), 'index.php') !== false;
        \PHPUnit_Framework_Assert::assertTrue($isRedirect, 'Apache redirect on backend does not work.');
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Apache redirect works correct.';
    }
}
