<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Css\PreProcessor\Adapter;

use Magento\Framework\App\State;

/**
 * Oyejorge adapter model
 */
class OyejorgeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Oyejorge
     */
    protected $model;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $objectManager->create('Magento\Framework\Css\PreProcessor\Adapter\Oyejorge');
        $this->state = $objectManager->get('Magento\Framework\App\State');
    }

    public function testProcess()
    {
        $sourceFilePath = realpath(__DIR__ . '/../_files/oyejorge.less');
        $expectedCss = ($this->state->getMode() === State::MODE_DEVELOPER)
            ? file_get_contents(__DIR__ . '/../_files/oyejorge_dev.css')
            : file_get_contents(__DIR__ . '/../_files/oyejorge.css');
        $this->assertEquals($expectedCss, $this->model->process($sourceFilePath));
    }

    /**
     *  Patch in MAGETWO-30317
     */
    public function testMinificatorProcess()
    {
        $sourceFilePath = realpath(__DIR__ . '/../_files/oyejorge.less');
        $options = array('relativeUrls' => false, 'compress' => true);
        $parser = new \Less_Parser($options);
        $parser->parseFile($sourceFilePath, '');
        $expectedCss = file_get_contents(__DIR__ . '/../_files/minificate.css');
        $this->assertEquals($expectedCss, $parser->getCss());
    }
}
