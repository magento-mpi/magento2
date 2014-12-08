<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Condition\Combine;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Model\Segment\Condition\Combine\Root
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configShare;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerSegment\Model\Segment\Condition\Combine\Root'
        );
    }

    /**
     * @dataProvider limitByStoreWebsiteDataProvider
     * @param int $website
     */
    public function testLimitByStoreWebsite($website)
    {
        $select = $this->getMock('Zend_Db_Select', ['join', 'where'], [], '', false);
        $select->expects(
            $this->once()
        )->method(
            'join'
        )->with(
            $this->arrayHasKey('store'),
            $this->equalTo('main.store_id=store.store_id'),
            $this->equalTo([])
        )->will(
            $this->returnSelf()
        );
        $select->expects(
            $this->once()
        )->method(
            'where'
        )->with(
            $this->equalTo('store.website_id IN (?)'),
            $this->equalTo($website)
        )->will(
            $this->returnSelf()
        );

        $testMethod = new \ReflectionMethod($this->_model, '_limitByStoreWebsite');
        $testMethod->setAccessible(true);

        $testMethod->invoke($this->_model, $select, $website, 'main.store_id');
    }

    public function limitByStoreWebsiteDataProvider()
    {
        return [[1], [new \Zend_Db_Expr(1)]];
    }
}
