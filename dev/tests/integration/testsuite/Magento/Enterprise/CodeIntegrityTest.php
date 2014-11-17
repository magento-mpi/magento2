<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Enterprise;

class CodeIntegrityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture current_store design/theme/theme_id 0
     */
    public function testGetConfigurationDesignThemeDefaults()
    {
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\View\Design'
        );
        $this->assertEquals('Magento/blank', $design->getConfigurationDesignTheme('frontend'));
        $this->assertEquals('Magento/backend', $design->getConfigurationDesignTheme('adminhtml'));
    }
}
