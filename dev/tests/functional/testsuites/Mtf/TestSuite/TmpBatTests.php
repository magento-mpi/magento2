<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;


class TmpBatTests
{
    public static function suite()
    {
        $suite = new TestSuite('BAT tmp');

        // Sales rule
        $suite->addTestSuite('Magento\SalesRule\Test\TestCase\BasicPromoTest');

        return $suite;
    }
}
