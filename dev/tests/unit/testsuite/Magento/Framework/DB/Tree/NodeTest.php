<?php
/**
 * \Magento\Framework\DB\Tree\Node test case
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\DB\Tree;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $data
     * @param $expectedException
     * @param $expectedExceptionMessage
     * @dataProvider constructorDataProvider
     */
    public function testConstructorWithInvalidArgumentsThrowsException(
        array $data, $expectedException, $expectedExceptionMessage
    ) {
        $this->setExpectedException($expectedException, $expectedExceptionMessage);
        new \Magento\Framework\DB\Tree\Node($data['node_data'], $data['keys']);
    }

    /**
     * @param array $data
     * @param string $assertMethod
     * @dataProvider isParentDataProvider
     */
    public function testIsParent(array $data, $assertMethod)
    {
        $model = new \Magento\Framework\DB\Tree\Node($data['node_data'], $data['keys']);
        $this->$assertMethod($model->isParent());
    }

    /**
     * @return array
     */
    public function isParentDataProvider()
    {
        return array(
            array(
                array(
                    'node_data' => array(
                        'id' => 'id',
                        'pid' => 'pid',
                        'level' => 'level',
                        'right_key' => 10,
                        'left_key' => 5
                    ),
                    'keys' => array(
                        'id' => 'id',
                        'pid' => 'pid',
                        'level' => 'level',
                        'right' => 'right_key',
                        'left' => 'left_key'
                    )
                ),
                'assertTrue'
            ),
            array(
                array(
                    'node_data' => array(
                        'id' => 'id',
                        'pid' => 'pid',
                        'level' => 'level',
                        'right_key' => 5,
                        'left_key' => 10
                    ),
                    'keys' => array(
                        'id' => 'id',
                        'pid' => 'pid',
                        'level' => 'level',
                        'right' => 'right_key',
                        'left' => 'left_key'
                    )
                ),
                'assertFalse'
            )
        );
    }

    /**
     * @return array
     */
    public function constructorDataProvider()
    {
        return array(
            array(
                array(
                    'node_data' => null,
                    'keys' => null
                ),
                '\Magento\Framework\DB\Tree\Node\NodeException',
                'Empty array of node information'
            ),
            array(
                array(
                    'node_data' => null,
                    'keys' => true
                ),
                '\Magento\Framework\DB\Tree\Node\NodeException',
                'Empty array of node information'
            ),
            array(
                array(
                    'node_data' => true,
                    'keys' => null
                ),
                '\Magento\Framework\DB\Tree\Node\NodeException',
                'Empty keys array'
            )
        );
    }
}
