<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\Config\Reader;

class DomTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $converterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $schemaLocatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validationStateMock;

    /**
     * @var \Magento\ObjectManager\Config\Reader\Dom
     */
    protected $model;

    protected function setUp()
    {
        $this->fileResolverMock = $this->getMock('\Magento\Config\FileResolverInterface');
        $this->converterMock = $this->getMock('\Magento\ObjectManager\Config\Mapper\Dom', array(), array(), '', false);
        $this->schemaLocatorMock = $this->getMock(
            '\Magento\ObjectManager\Config\SchemaLocator',
            array(),
            array(),
            '',
            false
        );
        $this->validationStateMock = $this->getMock(
            '\Magento\Config\ValidationStateInterface',
            array(),
            array(),
            '',
            false
        );

        $this->model = new \Magento\ObjectManager\Config\Reader\Dom(
            $this->fileResolverMock,
            $this->converterMock,
            $this->schemaLocatorMock,
            $this->validationStateMock,
            'filename.xml',
            array(),
            '\Magento\ObjectManager\Config\Reader\MergerMock'
        );
    }

    /**
     * @covers _createConfigMerger()
     */
    public function testRead()
    {
        $fileList = array('first content item');
        $this->fileResolverMock->expects($this->once())->method('get')->will($this->returnValue($fileList));
        $this->converterMock->expects($this->once())->method('convert')->with('reader dom result');
        $this->model->read();
    }
}


class MergerMock extends \PHPUnit_Framework_TestCase
{
    /**
     * @param null|string $initialContents
     * @param array $idAttributes
     * @param string $typeAttribute
     * @param $perFileSchema
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct($initialContents, $idAttributes, $typeAttribute, $perFileSchema)
    {
        $this->assertEquals('first content item', $initialContents);
        $this->assertEquals('xsi:type', $typeAttribute);

    }

    /**
     * @param $schemaFile
     * @param $errors
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validate($schemaFile, $errors)
    {
        return true;
    }

    public function getDom()
    {
        return 'reader dom result';
    }
}