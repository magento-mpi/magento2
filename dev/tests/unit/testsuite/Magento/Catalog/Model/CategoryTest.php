<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model;

class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Category
     */
    protected $model;

    /**
     * @var \Magento\Filter\FilterManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filter;

    protected function setUp()
    {
        $this->filter = $this->getMockBuilder('Magento\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->setMethods(['translitUrl'])
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Catalog\Model\Category', ['filter' => $this->filter]);
    }

    public function testFormatUrlKey()
    {
        $strIn = 'Some string';
        $resultString = 'some';

        $this->filter->expects($this->once())
            ->method('translitUrl')
            ->with($strIn)
            ->will($this->returnValue($resultString));

        $this->assertEquals($resultString, $this->model->formatUrlKey($strIn));
    }
}
