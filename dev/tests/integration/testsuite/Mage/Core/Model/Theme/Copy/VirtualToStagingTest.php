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
 * Test theme copy functionality
 */
class Mage_Core_Model_Theme_Copy_VirtualToStagingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Theme_Copy_VirtualToStaging
     */
    protected $_model;

    /**
     * @var Mage_Core_Model_Resource_Theme_Collection
     */
    protected $_collection;

    /**
     * Initialize Mage_Core_Model_Theme_Copy_VirtualToStaging model
     */
    protected function setUp()
    {
        $this->_model = $this->getMockBuilder('Mage_Core_Model_Theme_Copy_VirtualToStaging')
            ->setMethods(null)
            ->setConstructorArgs(array(Mage::getObjectManager()->get('Mage_Core_Model_Theme_Factory')))
            ->getMock();
        $this->_collection = Mage::getObjectManager()->create('Mage_Core_Model_Resource_Theme_Collection');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Mage/Core/_files/layout_update.php
     */
    public function testCopyVirtualToStaging()
    {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_collection->getThemeByFullPath('frontend/test/test');
        $this->assertNotEmpty($theme, 'Test theme not found');

        $stagingTheme = $this->_model->copy($theme);
        $this->assertNotEmpty($stagingTheme, 'Staging theme was not created');
        $this->assertSame($theme->getId(), $stagingTheme->getParentId());
    }
}
