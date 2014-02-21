<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidator()
    {
        $validator = new \Magento\Catalog\Model\Product\Validator();
        $productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $requestMock = $this->getMock('Magento\App\RequestInterface');
        $responseMock = $this->getMock('Magento\Object');
        $productMock->expects($this->once())->method('validate')->will($this->returnValue(true));
        $this->assertEquals(true, $validator->validate($productMock, $requestMock, $responseMock));
    }
}
