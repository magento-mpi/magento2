<?php
/**
 * {license_notice}
 *
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
        $scopeConfig = $this->getMockBuilder(
            'Magento\Framework\App\Config\ScopeConfigInterface'
        )->disableOriginalConstructor()->getMock();
        $scopeConfig->expects($this->atLeastOnce())->method('getValue')->will($this->returnValueMap($configData));

        $securityInfoMock = $this->getMock('Magento\Framework\Url\SecurityInfoInterface');
        $codeData = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $urlBuilder = $this->getMock(
            'Magento\Framework\Url',
            array('getBaseUrl'),
            array(
                $this->getMock('Magento\Framework\App\Route\ConfigInterface'),
                $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false),
                $securityInfoMock,
                $this->getMock('Magento\Framework\Url\ScopeResolverInterface', array(), array(), '', false),
                $this->getMock('Magento\Framework\Session\Generic', array(), array(), '', false),
                $this->getMock('Magento\Framework\Session\SidResolverInterface', array(), array(), '', false),
                $this->getMock('Magento\Framework\Url\RouteParamsResolverFactory', array(), array(), '', false),
                $this->getMock('Magento\Framework\Url\QueryParamsResolver', array(), array(), '', false),
                $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface', array(), array(), '', false),
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                array()
            )
        );
        $urlBuilder->expects(
            $this->any()
        )->method(
            'getBaseUrl'
        )->will(
            $this->returnValue('http://localhost/pub/media/')
        );

        $context = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\View\Element\Template\Context',
            array('scopeConfig' => $scopeConfig, 'urlBuilder' => $urlBuilder)
        );
        $storeManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\StoreManagerInterface'
        );
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Theme\Block\Html',
            array('storeManager' => $storeManager, 'urlHelperMock' => $codeData, 'context' => $context)
        );

        $this->assertEquals($returnValue, $block->getPrintLogoUrl());
    }

    public function getConfigValuesDataProvider()
    {
        return array(
            'sales_identity_logo_html' => array(
                array(
                    array(
                        'sales/identity/logo_html',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        null,
                        'image.gif'
                    )
                ),
                'http://localhost/pub/media/sales/store/logo_html/image.gif'
            ),
            'sales_identity_logo' => array(
                array(
                    array('sales/identity/logo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null, 'image.gif')
                ),
                'http://localhost/pub/media/sales/store/logo/image.gif'
            ),
            'sales_identity_logoTif' => array(
                array(
                    array('sales/identity/logo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null, 'image.tif')
                ),
                ''
            ),
            'sales_identity_logoTiff' => array(
                array(
                    array('sales/identity/logo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null, 'image.tiff')
                ),
                ''
            ),
            'no_logo' => array(array(), '')
        );
    }
}
