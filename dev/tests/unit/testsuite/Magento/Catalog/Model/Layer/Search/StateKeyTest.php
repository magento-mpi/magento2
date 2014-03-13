<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Search;

class StateKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \Magento\Catalog\Model\Layer\Search\StateKey
     */
    protected $model;

    protected function setUp()
    {
        $this->helperMock = $this->getMock('\Magento\CatalogSearch\Helper\Data', array(), array(), '', false);
        $this->model = new StateKey($this->helperMock);
    }

    /**
     * @covers \Magento\Catalog\Model\Layer\Search\StateKey::toString
     * @covers \Magento\Catalog\Model\Layer\Search\StateKey::__construct
     */
    public function testToString()
    {

    }
}
