<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../') . '/tools/migration/Acl/Generator.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../') . '/tools/migration/Acl/FileWriter.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../') . '/tools/migration/Acl/Formatter.php';

/**
 * Tools_Migration_Acl_Generator remove test case
 */
class Tools_Migration_Acl_GeneratorRemoveTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $model Tools_Migration_Acl_Generator
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
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_xmlFormatterMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileWriterMock;

    public function setUp()
    {
        $this->_xmlFormatterMock = $this->getMock('Tools_Migration_Acl_Formatter');
        $this->_fileWriterMock = $this->getMock('Tools_Migration_Acl_FileWriter');
        $this->_model = new Tools_Migration_Acl_Generator($this->_xmlFormatterMock, $this->_fileWriterMock);

        $fixturePath = realpath(__DIR__) . DIRECTORY_SEPARATOR . '_files';
        $path = $fixturePath . DIRECTORY_SEPARATOR . 'remove' . DIRECTORY_SEPARATOR;

        $this->_emptyFile = $path . 'empty.xml';
        $this->_notEmptyFile = $path . 'not_empty.xml';

        copy($this->_emptyFile . '.dist', $this->_emptyFile);
        copy($this->_notEmptyFile . '.dist', $this->_notEmptyFile);
    }

    public function tearDown()
    {
        unset($this->_model);
        unlink($this->_notEmptyFile);
    }

    public function testRemoveAdminhtmlFiles()
    {
        $domEmpty = new DOMDocument();
        $domEmpty->load($this->_emptyFile);

        $domNotEmpty = new DOMDocument();
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
        $this->assertFileExists($this->_notEmptyFile, 'Not empty file was removed');
        $this->assertFileNotExists($this->_emptyFile, 'Empty file was not removed');
        $this->assertEquals($expected, $result);
    }
}
