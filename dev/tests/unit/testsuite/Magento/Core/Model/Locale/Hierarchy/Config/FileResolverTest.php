<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Locale\Hierarchy\Config;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Locale\Hierarchy\Config\FileResolver
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_appDirsMock;

    protected function setUp()
    {
        $this->_appDirsMock = $this->getMock('Magento\Core\Model\Dir', array(), array(), '', false);
        $this->_model = new \Magento\Core\Model\Locale\Hierarchy\Config\FileResolver($this->_appDirsMock);
    }

    /**
     * @covers \Magento\Core\Model\Locale\Hierarchy\Config\FileResolver::get
     */
    public function testGet()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . '_files';

        $this->_appDirsMock->expects($this->once())
            ->method('getDir')
            ->with(\Magento\Core\Model\Dir::LOCALE)
            ->will($this->returnValue($path));

        $expectedFilesList = array(
            $path . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'hierarchy_config.xml',
            $path . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'hierarchy_config.xml'
        );

        $this->assertEquals($expectedFilesList, $this->_model->get('hierarchy_config.xml', 'scope'));
    }
}
