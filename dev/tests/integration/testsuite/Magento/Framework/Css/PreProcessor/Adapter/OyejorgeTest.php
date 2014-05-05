<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Css\PreProcessor\Adapter;

/**
 * Oyejorge adapter model
 */
class OyejorgeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Oyejorge
     */
    protected $model;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $objectManager->create('Magento\Framework\Css\PreProcessor\Adapter\Oyejorge');
    }

    public function testProcess()
    {
        $sourceFilePath = realpath(__DIR__ . '/../_files/oyejorge.less');
        $expectedCss = file_get_contents(__DIR__ . '/../_files/oyejorge.css');
        $actualCss = $this->model->process($sourceFilePath);
        $actualCss = str_replace("\n", "\r\n", $actualCss);
        $this->assertEquals($expectedCss, $actualCss);
    }
}
