<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Migration\Acl;

require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Generator.php';
require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/FileManager.php';
require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Formatter.php';

/**
 * Tools_Migration_Acl_Generator remove test case
 */
class GeneratorRemoveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $model \Magento\Tools\Migration\Acl\Generator
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_emptyFile;

    /**
     * @var string
     */
    protected $_notEmptyFile;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_xmlFormatterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileManagerMock;

    protected function setUp()
    {
        $fixturePath = realpath(__DIR__) . '/_files';
        $path = $fixturePath . '/remove/';

        $this->_emptyFile = $path . 'empty.xml';
        $this->_notEmptyFile = $path . 'not_empty.xml';

        $this->_xmlFormatterMock = $this->getMock('Magento\Tools\Migration\Acl\Formatter');
        $this->_fileManagerMock = $this->getMock('Magento\Tools\Migration\Acl\FileManager');
        $this->_fileManagerMock->expects($this->once())->method('remove')->with($this->equalTo($this->_emptyFile));
        $this->_model = new \Magento\Tools\Migration\Acl\Generator($this->_xmlFormatterMock, $this->_fileManagerMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testRemoveAdminhtmlFiles()
    {
        $domEmpty = new \DOMDocument();
        $domEmpty->load($this->_emptyFile);

        $domNotEmpty = new \DOMDocument();
        $domNotEmpty->load($this->_notEmptyFile);

        $adminhtmlDomList = array(
            $this->_emptyFile => $domEmpty,
            $this->_notEmptyFile => $domNotEmpty,
        );

        $this->_model->setAdminhtmlDomList($adminhtmlDomList);
        $expected = array(
            'removed' => array($this->_emptyFile),
            'not_removed' => array($this->_notEmptyFile),
            'artifacts' => array('AclXPathToAclId.log' => json_encode(array())),
        );

        $result = $this->_model->removeAdminhtmlFiles();
        $this->assertEquals($expected, $result);
    }
}
