<?php
/**
 * The list of test interfaces.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Module\Service;

interface FooV1Interface
{
    public function someMethod();
}

interface BarV1Interface
{
    public function someMethod();
}

interface FooBarV1Interface
{
    public function someMethod();
}

namespace Magento\Module\Service\Foo;

interface BarV1Interface
{
    public function someMethod();
}
