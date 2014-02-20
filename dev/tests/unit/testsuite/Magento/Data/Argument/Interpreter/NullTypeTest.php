<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Argument\Interpreter;

class NullTypeTest  extends \PHPUnit_Framework_TestCase
{
    public function testEvaluate()
    {
        $object = new NullType;
        $this->assertNull($object->evaluate(array('unused')));
    }
} 
