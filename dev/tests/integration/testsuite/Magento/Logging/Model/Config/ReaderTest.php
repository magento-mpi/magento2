<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model\Config;

/**
 * @magentoDataFixture Magento/Backend/controllers/_files/cache/all_types_disabled.php
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reader
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $fileResolver;

    protected function setUp()
    {
        $this->fileResolver = $this->getMockForAbstractClass('Magento\Framework\Config\FileResolverInterface');
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $objectManager->create(
            'Magento\Logging\Model\Config\Reader',
            ['fileResolver' => $this->fileResolver]
        );
    }

    public function testRead()
    {
        $this->fileResolver->expects($this->once())->method('get')->with('logging.xml', 'global')->willReturn(
            [file_get_contents(__DIR__ . '/_files/logging.xml')]
        );
        $expected = include __DIR__ . '/_files/expectedArray.php';
        $this->assertEquals($expected, $this->model->read('global'));
    }

    public function testMergeCompleteAndPartial()
    {
        $files = [
            file_get_contents(__DIR__ . '/_files/customerBalance.xml'),
            file_get_contents(__DIR__ . '/_files/Reward.xml')
        ];
        $this->fileResolver->expects($this->once())->method('get')->with('logging.xml', 'global')->willReturn($files);
        $this->assertArrayHasKey('logging', $this->model->read('global'));
    }
}
