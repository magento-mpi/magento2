<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Helper;

class PostDataTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPostData()
    {
        $url = '/controller/sample/action/url/';
        $product = ['product' => new \Magento\Framework\Object(['id' => 1])];
        $expected = json_encode([
            'action' => $url,
            'data' => [
                'product' => new \Magento\Framework\Object(['id' => 1]),
                \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED =>
                    strtr(base64_encode($url . 'for_uenc'), '+/=', '-_,')
            ]
        ]);

        $contextMock = $this->getMock(
            'Magento\Framework\App\Helper\Context',
            array('getUrlBuilder'),
            array(),
            '',
            false
        );
        $urlBuilderMock = $this->getMockForAbstractClass(
            'Magento\Framework\UrlInterface',
            array(),
            '',
            true,
            true,
            true,
            array('getCurrentUrl')
        );

        $contextMock->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilderMock));
        $urlBuilderMock->expects($this->once())
            ->method('getCurrentUrl')
            ->will($this->returnValue($url . 'for_uenc'));

        $model = new \Magento\Core\Helper\PostData($contextMock);

        $actual = $model->getPostData($url, $product);
        $this->assertEquals($expected, $actual);
    }
}
