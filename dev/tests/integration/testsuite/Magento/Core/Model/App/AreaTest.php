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

namespace Magento\Core\Model\App;

class AreaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\App\Area
     */
    protected $_model;

    public static function tearDownAfterClass()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')->
            cleanCache(array(\Magento\Core\Model\Design::CACHE_TAG));
    }

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode('frontend');
        /** @var $_model \Magento\Core\Model\App\Area */
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\App\Area', array('areaCode' => 'frontend'));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testInitDesign()
    {
        $defaultTheme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\DesignInterface')->setDefaultDesignTheme()->getDesignTheme();
        $this->_model->load(\Magento\Core\Model\App\Area::PART_DESIGN);
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\DesignInterface')
            ->setDefaultDesignTheme();

        $this->assertEquals($defaultTheme->getThemePath(), $design->getDesignTheme()->getThemePath());
        $this->assertEquals('frontend', $design->getArea());

        // try second time and make sure it won't load second time
        $this->_model->load(\Magento\Core\Model\App\Area::PART_DESIGN);
        $designArea = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\DesignInterface')
            ->getArea();
        $sameDesign = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\DesignInterface')
            ->setArea($designArea);
        $this->assertSame($design, $sameDesign);
    }

    // @codingStandardsIgnoreStart
    /**
     * @magentoConfigFixture current_store design/theme/ua_regexp a:1:{s:1:"_";a:2:{s:6:"regexp";s:10:"/firefox/i";s:5:"value";s:13:"magento_blank";}}
     * @magentoConfigFixture current_store design/package/ua_regexp a:1:{s:1:"_";a:2:{s:6:"regexp";s:10:"/firefox/i";s:5:"value";s:13:"magento_blank";}}
     * @magentoAppIsolation enabled
     */
    // @codingStandardsIgnoreEnd
    public function testDetectDesignUserAgent()
    {

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->create('Magento\TestFramework\Request');
        $request->setServer(array('HTTP_USER_AGENT' => 'Mozilla Firefox'));
        $this->_model->detectDesign($request);
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\DesignInterface');
        $this->assertEquals('magento_blank', $design->getDesignTheme()->getThemePath());
    }

    // @codingStandardsIgnoreStart
    /**
     * @magentoConfigFixture current_store design/theme/ua_regexp a:1:{s:1:"_";a:2:{s:6:"regexp";s:10:"/firefox/i";s:5:"value";s:13:"magento_blank";}}
     * @magentoConfigFixture current_store design/package/ua_regexp a:1:{s:1:"_";a:2:{s:6:"regexp";s:10:"/firefox/i";s:5:"value";s:13:"magento_blank";}}
     * @magentoDataFixture Magento/Core/_files/design_change.php
     * @magentoAppIsolation enabled
     */
    // @codingStandardsIgnoreEnd
    public function testDetectDesignDesignChange()
    {
        $this->_model->detectDesign();
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\DesignInterface');
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
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $model = $objectManager->create('Magento\Core\Model\App\Area', array('areaCode' => 'install'));
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->create('Magento\TestFramework\Request');
        $request->setServer(array('HTTP_USER_AGENT' => 'Mozilla Firefox'));
        $model->detectDesign($request);
        $design = $objectManager->get('Magento\View\DesignInterface');
        $this->assertNotEquals('magento_blank', $design->getDesignTheme()->getThemePath());
    }
}
