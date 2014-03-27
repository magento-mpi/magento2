<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Tools\View\Generator;

require_once __DIR__ . '/../../../../../../../../tools/Magento/Tools/View/Generator/Asset.php';
class AssetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\View\Generator\Asset
     */
    private $object;

    protected function setUp()
    {
        $pathGenerator = $this->getMock('Magento\View\Asset\PathGenerator');
        $this->object = new \Magento\Tools\View\Generator\Asset($pathGenerator, 'some/file.ext', 'area', 'theme');
    }

    public function testCreateRelative()
    {
        $relativeAsset = $this->object->createRelative('some/other/file.ext');
        $this->assertInstanceOf('\Magento\Tools\View\Generator\Asset', $relativeAsset);
        $this->assertNotSame($this->object, $relativeAsset);
        $this->assertSame('some/other/file.ext', $relativeAsset->getFilePath());
    }
}
