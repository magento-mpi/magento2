<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs\Block\Html\Head;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Element\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    /**
     * @var \Magento\RequireJs\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \Magento\RequireJs\Model\FileManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileManager;

    protected function setUp()
    {
        $this->context = $this->getMock('\Magento\Framework\View\Element\Context', array(), array(), '', false);
        $this->config = $this->getMock('\Magento\RequireJs\Config', array(), array(), '', false);
        $this->fileManager = $this->getMock('\Magento\RequireJs\Model\FileManager', array(), array(), '', false);
    }

    public function testGetAsset()
    {
        $asset = $this->getMockForAbstractClass('\Magento\Framework\View\Asset\LocalInterface');
        $this->fileManager->expects($this->once())->method('createRequireJsAsset')->will($this->returnValue($asset));
        $object = new Config($this->context, $this->config, $this->fileManager);;
        $this->assertSame($asset, $object->getAsset());
        $this->assertSame($asset, $object->getAsset(), 'Asset is supposed to be cached in-memory');
    }

    public function testToHtml()
    {
        $this->context->expects($this->once())
            ->method('getEventManager')
            ->will($this->returnValue($this->getMockForAbstractClass('\Magento\Event\ManagerInterface')))
        ;
        $this->context->expects($this->once())
            ->method('getScopeConfig')
            ->will($this->returnValue(
                $this->getMockForAbstractClass('\Magento\Framework\App\Config\ScopeConfigInterface')
            ))
        ;
        $this->config->expects($this->once())->method('getBaseConfig')->will($this->returnValue('the config data'));
        $object = new Config($this->context, $this->config, $this->fileManager);;
        $html = $object->toHtml();
        $expectedFormat = <<<expected
<script type="text/javascript">
the config data</script>
expected;
        $this->assertStringMatchesFormat($expectedFormat, $html);
    }
}
