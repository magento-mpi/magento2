<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\FileResolver;

class PrimaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Config\FileResolver\Primary
     */
    protected $_model;

    protected function setUp()
    {
        $appConfigDir = __DIR__ . DIRECTORY_SEPARATOR
            . '_files' . DIRECTORY_SEPARATOR . 'primary'
            . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'etc';

        $applicationDirsMock = $this->getMock('Magento\App\Dir', array(), array('getDir'), '', false);
        $applicationDirsMock->expects($this->any())
            ->method('getDir')
            ->with(\Magento\App\Dir::CONFIG)
            ->will($this->returnValue($appConfigDir));

        $this->_model = new \Magento\App\Config\FileResolver\Primary($applicationDirsMock);
    }

    /**
     * @param array $expectedResult
     * @param string $scope
     * @param string $filename
     * @dataProvider getMethodDataProvider
     */
    public function testGet(array $expectedResult, $scope, $filename)
    {
        $this->assertEquals($expectedResult, $this->_model->get($filename, $scope));
    }

    /**
     * @return array
     */
    public function getMethodDataProvider()
    {
        $appConfigDir = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'primary'
            . DIRECTORY_SEPARATOR .  'app' . DIRECTORY_SEPARATOR . 'etc';

        return array(
            array(
                array(
                    $appConfigDir . DIRECTORY_SEPARATOR . 'di.xml',
                    $appConfigDir . DIRECTORY_SEPARATOR . 'some_config' .DIRECTORY_SEPARATOR.  'di.xml',
                ),
                'primary',
                'di.xml',
            )
        );
    }
}
