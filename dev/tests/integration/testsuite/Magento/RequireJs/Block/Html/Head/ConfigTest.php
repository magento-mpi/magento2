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
     * @var \Magento\RequireJs\Block\Html\Head\Config
     */
    private $object;

    protected function setUp()
    {
        $this->object = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\RequireJs\Block\Html\Head\Config');
    }

    public function testGetAsset()
    {
        /** @var \Magento\View\Asset\PublicFile $asset */
        $asset = $this->object->getAsset();
        $this->assertSame(\Magento\View\Publisher::CONTENT_TYPE_JS, $asset->getContentType());
        $configFile = $asset->getSourceFile();
        $this->assertFileExists($configFile);
    }

    public function testToHtml()
    {
        $html = $this->object->toHtml();
        $expectedFormat = <<<expected
<script type="text/javascript">
require.config({
    "baseUrl": %s,
    "paths": {
        "magento": "mage/requirejs/plugin/id-normalizer"
    }
});
</script>
expected;
        $this->assertStringMatchesFormat($expectedFormat, $html);
    }
}
