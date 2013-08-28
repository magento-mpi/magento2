<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(__DIR__ . '/../../../../../../../') . '/tools/migration/System/Writer/Factory.php';
require_once realpath(__DIR__ . '/../../../../../../../') . '/tools/migration/System/Writer/FileSystem.php';

class Tools_Migration_System_Writer_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tools_Migration_System_Writer_Factory
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Tools_Migration_System_Writer_Factory();
    }

    public function testGetWriterReturnsProperWriter()
    {
        $this->assertInstanceOf('Tools_Migration_System_Writer_FileSystem', $this->_model->getWriter('write'));
        $this->assertInstanceOf('Tools_Migration_System_Writer_Memory', $this->_model->getWriter('someWriter'));
    }
}
