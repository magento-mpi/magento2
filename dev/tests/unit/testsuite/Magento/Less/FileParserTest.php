<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Less;

class FileParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Less\Instruction\ImportFactory
     */
    protected $importFactory;

    /**
     * @var \Magento\Less\FileParser
     */
    protected $parseModel;

    protected  function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->importFactory = $this->getMock(
            'Magento\Less\Instruction\ImportFactory',
            array('create')
        );

        $this->importFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue('value'));

        /** @var \Magento\Less\FileParser $parserModel */
        $this->parserModel = $this->objectManager->getObject(
            'Magento\Less\FileParser',
            array('importFactory' => $this->importFactory)
        );

    }

    public function testParseValidFile()
    {
        $content = $this->getLessFileContent('valid.less');
        $importArray = $this->parserModel->parse($content);
        $this->assertCount(2, $importArray);
    }

    public function testParseInvalidFile()
    {
        $content = $this->getLessFileContent('invalid.less');
        $importArray = $this->parserModel->parse($content);
        $this->assertEmpty($importArray);
    }

    protected function getLessFileContent($fileName)
    {
        $lessFile = realpath( __DIR__ . '/_files/' . $fileName);
        return file_get_contents($lessFile);
    }
}
