<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Writer/Factory.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Writer/FileSystem.php';

class Magento_Test_Tools_Migration_System_Writer_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Migration\System\Writer\Factory
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Tools\Migration\System\Writer\Factory();
    }

    public function testGetWriterReturnsProperWriter()
    {
        $this->assertInstanceOf('Magento\Tools\Migration\System\Writer\FileSystem', $this->_model->getWriter('write'));
        $this->assertInstanceOf('Magento\Tools\Migration\System\Writer\Memory', $this->_model->getWriter('someWriter'));
    }
}
