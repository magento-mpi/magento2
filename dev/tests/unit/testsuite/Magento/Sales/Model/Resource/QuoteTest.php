<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource;

class QuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Resource\Quote
     */
    protected $_model;

    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $_resourceMock;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_configMock;

    /**
     * @var \Magento\DB\Adapter\Pdo\Mysql
     */
    protected $_adapterMock;

    /**
     * @var \Magento\DB\Select
     */
    protected $_selectMock;

    protected function setUp()
    {
        $this->_selectMock = $this->getMock('\Magento\DB\Select', array(), array(), '', false);
        $this->_selectMock->expects($this->any())->method('from')->will($this->returnSelf());
        $this->_selectMock->expects($this->any())->method('where');

        $this->_adapterMock = $this->getMock('\Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false);
        $this->_adapterMock->expects($this->any())->method('select')->will($this->returnValue($this->_selectMock));

        $this->_resourceMock = $this->getMock('\Magento\Framework\App\Resource', array(), array(), '', false);
        $this->_resourceMock->expects(
            $this->any()
        )->method(
            'getConnection'
        )->will(
            $this->returnValue($this->_adapterMock)
        );

        $this->_configMock = $this->getMock('\Magento\Eav\Model\Config', array(), array(), '', false);

        $this->_model = new \Magento\Sales\Model\Resource\Quote(
            $this->_resourceMock,
            new \Magento\Stdlib\DateTime(),
            $this->_configMock
        );
    }

    /**
     * @param $value
     * @dataProvider isOrderIncrementIdUsedDataProvider
     */
    public function testIsOrderIncrementIdUsed($value)
    {
        $expectedBind = array(':increment_id' => $value);
        $this->_adapterMock->expects($this->once())->method('fetchOne')->with($this->_selectMock, $expectedBind);
        $this->_model->isOrderIncrementIdUsed($value);
    }

    /**
     * @return array
     */
    public function isOrderIncrementIdUsedDataProvider()
    {
        return array(array(100000001), array('10000000001'), array('M10000000001'));
    }
}
