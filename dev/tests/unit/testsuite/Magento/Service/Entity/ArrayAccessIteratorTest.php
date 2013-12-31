<?php
/**
 * Test ArrayAccessIterator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Entity;

class ArrayAccessIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayIterating()
    {
        $wrappedArray = ['a', 'b', 'c'];
        $arrayObject = new \ArrayObject($wrappedArray);

        $arrayIterator = new ArrayAccessIterator($arrayObject);

        $count = 0;
        foreach ($arrayIterator as $key => $value) {
            $this->assertSame($wrappedArray[$key], $value);
            $count++;
        }
        $this->assertEquals(3, $count);
    }

    public function testMapIterating()
    {
        $wrappedArray = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];
        $arrayObject = new \ArrayObject($wrappedArray);

        $arrayIterator = new ArrayAccessIterator($arrayObject, array_keys($wrappedArray));

        $count = 0;
        foreach ($arrayIterator as $key => $value) {
            $this->assertSame($wrappedArray[$key], $value);
            $count++;
        }
        $this->assertEquals(3, $count);
    }

    public function testEmptyIterating()
    {
        $arrayObject = new \ArrayObject();

        $arrayIterator = new ArrayAccessIterator($arrayObject);

        $count = 0;
        foreach ($arrayIterator as $key => $value) {
            $this->assertNull($key);
            $this->assertNull($value);
            $count++;
        }
        $this->assertEquals(0, $count);
    }
}
