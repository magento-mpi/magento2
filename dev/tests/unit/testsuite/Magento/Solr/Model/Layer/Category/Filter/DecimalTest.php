<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Layer\Category\Filter;

class DecimalTest extends \PHPUnit_Framework_TestCase
{
    public function testApplySignature()
    {
        $requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $requestMock->expects($this->any())
            ->method('getParam')
            ->willReturn(false);
        $decFactMock = $this->getMock('Magento\Catalog\Model\Layer\Filter\DataProvider\DecimalFactory', ['create']);
        $decimal = (new \Magento\TestFramework\Helper\ObjectManager($this))
            ->getObject('Magento\Solr\Model\Layer\Category\Filter\Decimal',
                [
                    'dataProviderFactory' => $decFactMock
                ]);
        // Verify bug fix - Test that method call does not produce fatal error
        $this->assertSame($decimal, $decimal->apply($requestMock));
    }
}
