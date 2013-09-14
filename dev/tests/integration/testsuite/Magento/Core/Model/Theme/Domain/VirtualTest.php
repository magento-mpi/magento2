<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Theme_Domain_VirtualTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $_themes = array(
        'physical' => array(
            'parent_id'     => null,
            'theme_path'    => 'test/test',
            'theme_version' => '1.0.0.0',
            'theme_title'   => 'Test physical theme',
            'area'          => Magento_Core_Model_App_Area::AREA_FRONTEND,
            'type'          => Magento_Core_Model_Theme::TYPE_PHYSICAL
        ),
        'virtual' => array(
            'parent_id'     => null,
            'theme_path'    => '',
            'theme_version' => '1.0.0.0',
            'theme_title'   => 'Test virtual theme',
            'area'          => Magento_Core_Model_App_Area::AREA_FRONTEND,
            'type'          => Magento_Core_Model_Theme::TYPE_VIRTUAL
        ),
        'staging' => array(
            'parent_id'     => null,
            'theme_path'    => '',
            'theme_version' => '1.0.0.0',
            'theme_title'   => 'Test staging theme',
            'area'          => Magento_Core_Model_App_Area::AREA_FRONTEND,
            'type'          => Magento_Core_Model_Theme::TYPE_STAGING
        ),
    );

    /**
     * @var int
     */
    protected $_physicalThemeId;

    /**
     * @var int
     */
    protected $_virtualThemeId;

    /**
     * @magentoDbIsolation enabled
     */
    public function testGetPhysicalTheme()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        //1. set up fixture
        /** @var $physicalTheme Magento_Core_Model_Theme */
        $physicalTheme = $objectManager->create('Magento_Core_Model_Theme');
        $physicalTheme->setData($this->_themes['physical']);
        $physicalTheme->save();

        $this->_themes['virtual']['parent_id'] = $physicalTheme->getId();

        /** @var $virtualTheme Magento_Core_Model_Theme */
        $virtualTheme = $objectManager->create('Magento_Core_Model_Theme');
        $virtualTheme->setData($this->_themes['virtual']);
        $virtualTheme->save();

        $this->_themes['staging']['parent_id'] = $virtualTheme->getId();

        /** @var $stagingTheme Magento_Core_Model_Theme */
        $stagingTheme = $objectManager->create('Magento_Core_Model_Theme');
        $stagingTheme->setData($this->_themes['staging']);
        $stagingTheme->save();

        $this->_physicalThemeId = $physicalTheme->getId();
        $this->_virtualThemeId = $virtualTheme->getId();

        //2. run test
        /** @var $virtualTheme Magento_Core_Model_Theme */
        $virtualTheme = $objectManager->create('Magento_Core_Model_Theme');
        $virtualTheme->load($this->_virtualThemeId);

        $this->assertEquals(
            $this->_physicalThemeId,
            $virtualTheme->getDomainModel(Magento_Core_Model_Theme::TYPE_VIRTUAL)->getPhysicalTheme()->getId()
        );
    }

    protected function tearDown()
    {
        unset($this->_physicalThemeId);
        unset($this->_virtualThemeId);
    }
}
