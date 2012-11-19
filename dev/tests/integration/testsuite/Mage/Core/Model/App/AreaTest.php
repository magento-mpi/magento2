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

class Mage_Core_Model_App_AreaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_App_Area
     */
    protected $_model;

    public function setUp()
    {
        /** @var $_model Mage_Core_Model_App_Area */
        $this->_model = Mage::getModel('Mage_Core_Model_App_Area', array('areaCode' => 'frontend'));
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testInitDesign()
    {
        $defaultTheme = Mage::getDesign()->setDefaultDesignTheme()->getDesignTheme();
        $this->_model->load(Mage_Core_Model_App_Area::PART_DESIGN);
        $design = Mage::getDesign();

        $this->assertEquals($defaultTheme->getThemePath(), $design->getDesignTheme()->getThemePath());
        $this->assertEquals('frontend', $design->getArea());

        // try second time and make sure it won't load second time
        $this->_model->load(Mage_Core_Model_App_Area::PART_DESIGN);
        $this->assertSame($design, Mage::getDesign());
    }

    /**
     * @magentoConfigFixture adminhtml/design/theme/full_name default/basic
     * @magentoAppIsolation enabled
     */
    public function testDetectDesignGlobalConfig()
    {
        $model = Mage::getModel('Mage_Core_Model_App_Area', array('areaCode' => 'adminhtml'));
        $model->detectDesign();
        $this->assertEquals('default/basic', Mage::getDesign()->getDesignTheme()->getThemePath());
    }

    /**
     * @magentoConfigFixture current_store design/theme/full_name default/blank
     * @magentoAppIsolation enabled
     */
    public function testDetectDesignStoreConfig()
    {
        $this->_model->detectDesign();
        $this->assertEquals('default/blank', Mage::getDesign()->getDesignTheme()->getThemePath());
    }

    // @codingStandardsIgnoreStart
    /**
     * @magentoConfigFixture current_store design/theme/ua_regexp a:1:{s:1:"_";a:2:{s:6:"regexp";s:10:"/firefox/i";s:5:"value";s:14:"default/modern";}}
     * @magentoAppIsolation enabled
     */
    // @codingStandardsIgnoreEnd
    public function testDetectDesignUserAgent()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla Firefox';
        $this->_model->detectDesign(new Zend_Controller_Request_Http);
        $this->assertEquals('default/modern', Mage::getDesign()->getDesignTheme()->getThemePath());
    }

    /**
     * @magentoDataFixture Mage/Core/_files/design_change.php
     * @magentoAppIsolation enabled
     */
    public function testDetectDesignDesignChange()
    {
        $this->_model->detectDesign();
        $this->assertEquals('default/modern', Mage::getDesign()->getDesignTheme()->getThemePath());
    }

    // @codingStandardsIgnoreStart
    /**
     * Test that non-frontend areas are not affected neither by user-agent reg expressions, nor by the "design change"
     *
     * @magentoConfigFixture current_store design/theme/ua_regexp a:1:{s:1:"_";a:2:{s:6:"regexp";s:10:"/firefox/i";s:5:"value";s:22:"default/demo_blue";}}
     * magentoDataFixture Mage/Core/_files/design_change.php
     * @magentoAppIsolation enabled
     */
    // @codingStandardsIgnoreEnd
    public function testDetectDesignNonFrontend()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla Firefox';
        $model = Mage::getModel('Mage_Core_Model_App_Area', array('areaCode' => 'install'));
        $model->detectDesign(new Zend_Controller_Request_Http);
        $this->assertNotEquals('default/modern', Mage::getDesign()->getDesignTheme()->getThemePath());
        $this->assertNotEquals('default/demo_blue', Mage::getDesign()->getDesignTheme()->getThemePath());
    }
}
