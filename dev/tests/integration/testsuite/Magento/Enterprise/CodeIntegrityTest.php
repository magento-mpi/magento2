<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @subpackage  integration_tests
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
        $design = Mage::getModel('Magento\Core\Model\View\Design');
        $this->assertEquals('magento_fixed_width', $design->getConfigurationDesignTheme('frontend'));
        $this->assertEquals('magento_enterprise', $design->getConfigurationDesignTheme('install'));
        $this->assertEquals('magento_backend', $design->getConfigurationDesignTheme('adminhtml'));
    }
}
