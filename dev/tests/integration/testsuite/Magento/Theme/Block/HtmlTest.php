<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getConfigValuesDataProvider
     * @magentoAppArea frontend
     */
    public function testGetPrintLogoUrl($configData, $returnValue)
    {
        $storeConfig = $this->getMockBuilder('Magento\Core\Model\Store\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $storeConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValueMap($configData));

        $securityInfoMock = $this->getMock('Magento\Url\SecurityInfoInterface');
        $codeData = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $urlBuilder = $this->getMock(
            'Magento\Core\Model\Url',
            array('getBaseUrl'),
            array(
                $this->getMock('Magento\App\Route\ConfigInterface'),
                $this->getMock('Magento\App\Request\Http', array(), array(), '', false),
                $securityInfoMock,
                $this->getMock('Magento\Core\Model\App', array(), array(), '', false),
                $this->getMock('Magento\Url\ScopeResolverInterface', array(), array(), '', false),
                $this->getMock('Magento\Core\Model\Session', array(), array(), '', false),
                $this->getMock('Magento\Session\SidResolverInterface', array(), array(), '', false),
                $this->getMock(
                    'Magento\Url\RouteParamsResolverFactory', array(), array(), '', false
                ),
                null,
                array()
            )
        );
        $urlBuilder->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/pub/media/'));

        $context = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\View\Element\Template\Context', array(
            'storeConfig' => $storeConfig,
            'urlBuilder' => $urlBuilder,
        ));
        $storeManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface');
        $locale = $this->getMock('Magento\Core\Model\LocaleInterface', array(), array(), '', false);
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Theme\Block\Html', array(
                'storeManager'  => $storeManager,
                'locale'        => $locale,
                'urlHelperMock' => $codeData,
                'context'       => $context
            ));

        $this->assertEquals($returnValue, $block->getPrintLogoUrl());
    }

    public function getConfigValuesDataProvider()
    {
        return array(
            'sales_identity_logo_html' => array(
                array(array('sales/identity/logo_html', null, 'image.gif')),
                'http://localhost/pub/media/sales/store/logo_html/image.gif'
            ),
            'sales_identity_logo' => array(
                array(array('sales/identity/logo', null, 'image.gif')),
                'http://localhost/pub/media/sales/store/logo/image.gif'
            ),
            'sales_identity_logoTif' => array(
                array(array('sales/identity/logo', null, 'image.tif')),
                ''
            ),
            'sales_identity_logoTiff' => array(
                array(array('sales/identity/logo', null, 'image.tiff')),
                ''
            ),
            'no_logo' => array(
                array(),
                ''
            ),
        );
    }
}
