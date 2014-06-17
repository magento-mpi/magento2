<?php
/**
 * \Magento\Framework\DB\Helper\AbstractHelper test case
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\DB\Helper;

class AbstractHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\DB\Helper\AbstractHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterMock;

    protected function setUp()
    {
        $this->_adapterMock = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface');

        $this->_resourceMock = $this->getMock('Magento\Framework\App\Resource', array(), array(), '', false);
        $this->_resourceMock->expects($this->any())
            ->method('getConnection')
            ->with('prefix_read')
            ->will($this->returnValue($this->_adapterMock));

        $this->_model = $this->getMockForAbstractClass(
            'Magento\Framework\DB\Helper\AbstractHelper',
            array($this->_resourceMock, 'prefix'),
            '',
            true,
            true,
            true,
            array('addLikeEscape')
        );
    }

    /**
     * @param string $expected
     * @param array $data
     * @dataProvider escapeLikeValueDataProvider
     */
    public function testEscapeLikeValue($expected, array $data)
    {
        $this->assertEquals($expected, $this->_model->escapeLikeValue($data['value'], $data['options']));
    }

    public function testGetCILike()
    {
        $field = 'field';
        $value = 'value';
        $options = array();

        $this->_adapterMock->expects($this->once())
            ->method('quoteIdentifier')
            ->with($field)
            ->will($this->returnArgument(0));

        $this->_model->expects($this->once())
            ->method('addLikeEscape')
            ->with($value, $options)
            ->will($this->returnArgument(0));

        $result = $this->_model->getCILike($field, $value, $options);
        $this->assertInstanceOf('Zend_Db_Expr', $result);
        $this->assertEquals($field . ' LIKE ' . $value, (string)$result);
    }

    /**
     * @return array
     */
    public function escapeLikeValueDataProvider()
    {
        return array(
            array(
                '',
                array(
                    'value' => '',
                    'options' => array()
                )
            ),
            array(
                'LIKE \%string\_end',
                array(
                    'value' => 'LIKE %string_end',
                    'options' => array()
                )
            ),
            array(
                'LIKE \%string_end',
                array(
                    'value' => 'LIKE %string_end',
                    'options' => array(
                        'allow_symbol_mask' => true
                    )
                )
            ),
            array(
                'LIKE %string\_end',
                array(
                    'value' => 'LIKE %string_end',
                    'options' => array(
                        'allow_string_mask' => true
                    )
                )
            ),
            array(
                'LIKE %string_end',
                array(
                    'value' => 'LIKE %string_end',
                    'options' => array(
                        'allow_symbol_mask' => true,
                        'allow_string_mask' => true
                    )
                )
            ),
            array(
                '%string%',
                array(
                    'value' => 'string',
                    'options' => array(
                        'position' => 'any'
                    )
                )
            ),
            array(
                'string%',
                array(
                    'value' => 'string',
                    'options' => array(
                        'position' => 'start'
                    )
                )
            ),
            array(
                '%string',
                array(
                    'value' => 'string',
                    'options' => array(
                        'position' => 'end'
                    )
                )
            )
        );
    }
}
