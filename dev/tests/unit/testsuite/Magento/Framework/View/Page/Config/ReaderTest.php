<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\Page\Config;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Test for page config reader model
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var \Magento\Framework\View\Page\Config\Structure|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $structureMock;

    /**
     * @var \Magento\Framework\View\Layout\Element|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $elementMock;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->structureMock = $this->getMockBuilder('Magento\Framework\View\Page\Config\Structure')
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->reader = $this->objectManagerHelper->getObject(
            'Magento\Framework\View\Page\Config\Reader',
            [
                'structure' => $this->structureMock
            ]
        );
    }

    protected function tearDown()
    {
        unset($this->structureMock);
        unset($this->reader);
    }

    /**
     * @param string $filePath
     * @dataProvider readHeadDataProvider
     */
    public function testReadHead($filePath)
    {
        $this->elementMock = new \Magento\Framework\View\Layout\Element(file_get_contents(__DIR__ . $filePath));
        $this->assertInstanceOf(
            '\Magento\Framework\View\Page\Config\Reader',
            $this->reader->readHead($this->elementMock)
        );
    }

    public function readHeadDataProvider()
    {
        return [
            ['/__files/template.xml'],
            ['/__files/template_default.xml']
        ];
    }
}
