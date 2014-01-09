<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Arguments\FileResolver;

class PrimaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $fileList
     * @param string $scope
     * @param string $filename
     * @dataProvider getMethodDataProvider
     */
    public function testGet(array $fileList, $scope, $filename)
    {
        $directory = $this->getMock('Magento\Filesystem\Directory\Read', array('search'), array(), '', false);
        $filesystem = $this->getMock('Magento\Filesystem', array('getDirectoryRead'), array(), '', false);
        $iteratorFactory = $this->getMock(
            'Magento\Config\FileIteratorFactory', array('create'), array(), '', false
        );

        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\Filesystem::CONFIG)
            ->will($this->returnValue($directory));

        $directory->expects($this->once())
            ->method('search')
            ->will($this->returnValue($fileList));

        $iteratorFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue(true));

        $model = new \Magento\App\Arguments\FileResolver\Primary($filesystem, $iteratorFactory);

        $this->assertTrue($model->get($filename, $scope));
    }

    /**
     * @return array
     */
    public function getMethodDataProvider()
    {
        return array(
            array(
                array(
                    'config/di.xml',
                    'config/some_config/di.xml',
                ),
                'primary',
                'di.xml',
            )
        );
    }
}
