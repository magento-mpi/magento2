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

class Magento_Core_Model_App_AreaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_App_Area
     */
    protected $_model;

    public static function tearDownAfterClass()
    {
        Mage::app()->cleanCache(array(Magento_Core_Model_Design::CACHE_TAG));
    }

    public function setUp()
    {
        /** @var $_model Magento_Core_Model_App_Area */
        $this->_model = Mage::getModel('Magento_Core_Model_App_Area', array('areaCode' => 'frontend'));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testInitDesign()
    {
        $defaultTheme = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_View_DesignInterface')->setDefaultDesignTheme()->getDesignTheme();
        $this->_model->load(Magento_Core_Model_App_Area::PART_DESIGN);
        $design = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setDefaultDesignTheme();

        $this->assertEquals($defaultTheme->getThemePath(), $design->getDesignTheme()->getThemePath());
        $this->assertEquals('frontend', $design->getArea());

        // try second time and make sure it won't load second time
        $this->_model->load(Magento_Core_Model_App_Area::PART_DESIGN);
        $designArea = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->getArea();
        $sameDesign = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea($designArea);
        $this->assertSame($design, $sameDesign);
    }

    // @codingStandardsIgnoreStart
    /**
     * @magentoConfigFixture current_store design/theme/ua_regexp a:1:{s:1:"_";a:2:{s:6:"regexp";s:10:"/firefox/i";s:5:"value";s:13:"magento_blank";}}
     * @magentoAppIsolation enabled
     */
    // @codingStandardsIgnoreEnd
    public function testDetectDesignUserAgent()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla Firefox';
        $this->_model->detectDesign(new Zend_Controller_Request_Http);
        $design = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface');
        $this->assertEquals('magento_blank', $design->getDesignTheme()->getThemePath());
    }

    /**
     * @magentoDataFixture Magento/Core/_files/design_change.php
     * @magentoAppIsolation enabled
     */
    public function testDetectDesignDesignChange()
    {
        $this->_model->detectDesign();
        $design = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface');
        $this->assertEquals('magento_blank', $design->getDesignTheme()->getThemePath());
    }

    // @codingStandardsIgnoreStart
    /**
     * Test that non-frontend areas are not affected neither by user-agent reg expressions, nor by the "design change"
     *
     * @magentoConfigFixture current_store design/theme/ua_regexp a:1:{s:1:"_";a:2:{s:6:"regexp";s:10:"/firefox/i";s:5:"value";s:13:"magento_blank";}}
     * magentoDataFixture Magento/Core/_files/design_change.php
     * @magentoAppIsolation enabled
     */
    // @codingStandardsIgnoreEnd
    public function testDetectDesignNonFrontend()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla Firefox';
        $model = Mage::getModel('Magento_Core_Model_App_Area', array('areaCode' => 'install'));
        $model->detectDesign(new Zend_Controller_Request_Http);
        $design = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface');
        $this->assertNotEquals('magento_blank', $design->getDesignTheme()->getThemePath());
    }
}
