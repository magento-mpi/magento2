<?php
/**
 * \Magento\Framework\Object\Copy\Config\Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Object\Copy\Config;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\App\Cache\State;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Object\Copy\Config\Reader
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $fileResolver;

    public static function setUpBeforeClass()
    {
        Bootstrap::getInstance()->reinitialize([State::PARAM_BAN_CACHE => true]);
    }

    public function setUp()
    {
        $this->fileResolver = $this->getMockForAbstractClass('Magento\Framework\Config\FileResolverInterface');
        $objectManager = Bootstrap::getObjectManager();
        $this->model = $objectManager->create(
            'Magento\Framework\Object\Copy\Config\Reader',
            array('fileResolver' => $this->fileResolver)
        );
    }

    public function testRead()
    {
        $this->fileResolver->expects($this->once())
            ->method('get')
            ->with('fieldset.xml', 'global')
            ->willReturn([file_get_contents(__DIR__ . '/_files/fieldset.xml')]);
        $expected = include __DIR__ . '/_files/expectedArray.php';
        $this->assertEquals($expected, $this->model->read('global'));
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = [
            file_get_contents(__DIR__ . '/_files/partialFieldsetFirst.xml'),
            file_get_contents(__DIR__ . '/_files/partialFieldsetSecond.xml')
        ];
        $this->fileResolver->expects($this->once())
            ->method('get')
            ->with('fieldset.xml', 'global')
            ->willReturn($fileList);
        $expected = array(
            'global' => array(
                'sales_convert_quote_item' => array(
                    'event_id' => array('to_order_item' => "*"),
                    'event_name' => array('to_order_item' => "*"),
                    'event_description' => array('to_order_item' => "complexDesciption")
                )
            )
        );
        $this->assertEquals($expected, $this->model->read('global'));
    }
}
