<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme service model
 */
class Mage_Core_Model_Theme_ServiceTest extends PHPUnit_Framework_TestCase
{
    public function testGetNotCustomizedFrontThemes()
    {
        /** @var $themeService Mage_Core_Model_Theme_Service */
        $themeService = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Service');
        $collection = $themeService->getPhysicalThemes(1,
            Mage_Core_Model_Resource_Theme_Collection::DEFAULT_PAGE_SIZE);

        $this->assertLessThanOrEqual(
            Mage_Core_Model_Resource_Theme_Collection::DEFAULT_PAGE_SIZE, $collection->count()
        );

        /** @var $theme Mage_Core_Model_Theme */
        foreach ($collection as $theme) {
            $this->assertEquals('frontend', $theme->getArea());
            $this->assertFalse($theme->isVirtual());
        }
    }
}
