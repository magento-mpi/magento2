<?php
//namespace Vendor\Package;

use FooInterface;
use BarClass as Bar;
//use OtherVendor\OtherPackage\BazClass;

/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Foo extends Bar implements FooInterface
{
    public function sampleFunction($a, $b = null)
    {
        $foo = $this;
        $arg1 = "";
        $arg2 = "";
        $arg3 = "";
        if ($a === $b) {
            bar();
        } elseif ($a > $b) {
            $foo->bar($arg1);
        } else {
            BazClass::bar($arg2, $arg3);
        }
    }

    final public static function bar()
    {
        // method body
    }
}