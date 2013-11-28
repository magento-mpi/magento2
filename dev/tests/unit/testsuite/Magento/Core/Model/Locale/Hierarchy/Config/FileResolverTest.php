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
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    protected function setUp()
    {
        $this->_filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_model = new \Magento\Core\Model\Locale\Hierarchy\Config\FileResolver($this->_filesystem);
    }

    /**
     * @covers \Magento\Core\Model\Locale\Hierarchy\Config\FileResolver::get
     */
    public function testGet()
    {
        $path = __DIR__ . '/_files';

        $this->_filesystem->expects($this->once())
            ->method('getPath')
            ->with(\Magento\Filesystem::LOCALE)
            ->will($this->returnValue($path));

        $expectedFilesList = array(
            $path . '/custom/hierarchy_config.xml',
            $path . '/default/hierarchy_config.xml'
        );

        $this->assertEquals($expectedFilesList, $this->_model->get('hierarchy_config.xml', 'scope'));
    }
}
