<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for \Magento\Webapi\Model\Source\Acl\Role.
 */
namespace Magento\Webapi\Model\Source\Acl;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check output format.
     *
     * @dataProvider toOptionsHashDataProvider
     *
     * @param bool $addEmpty
     * @param array $data
     * @param array $expected
     */
    public function testToOptionHashFormat($addEmpty, $data, $expected)
    {
        $resourceMock = $this->getMockBuilder('Magento\Webapi\Model\Resource\Acl\Role')
            ->setMethods(array('getRolesList'))
            ->disableOriginalConstructor()
            ->getMock();
        $resourceMock->expects($this->any())
            ->method('getRolesList')
            ->will($this->returnValue($data));

        $model = new \Magento\Webapi\Model\Source\Acl\Role(array(
            'resource' => $resourceMock
        ));

        $options = $model->toOptionHash($addEmpty);
        $this->assertEquals($expected, $options);
    }

    /**
     * Data provider for testing toOptionHash.
     *
     * @return array
     */
    public function toOptionsHashDataProvider()
    {
        return array(
            'with empty' => array(
                true, array('1' => 'role 1', '2' => 'role 2'), array('' => '', '1' => 'role 1', '2' => 'role 2')
            ),
            'without empty' => array(
                false, array('1' => 'role 1', '2' => 'role 2'), array('1' => 'role 1', '2' => 'role 2')
            ),
        );
    }
}
