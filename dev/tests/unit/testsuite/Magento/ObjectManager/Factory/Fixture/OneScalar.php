<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Factory\Fixture;

/**
 * A "value object" style constructor that requires one non-injectable argument
 */
class OneScalar
{
    /**
     * @var string
     */
    private $foo;

    /**
     * @param string $foo
     */
    public function __construct($foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }
}
