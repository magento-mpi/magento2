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
        $formKey = 'formKey16bit';
        $product = ['product' => new \Magento\Object(['id' => 1])];
        $expectedArray = [
            'action' => $url,
            'data' => [
                'product' => new \Magento\Object(['id' => 1]),
                'formKey' => $formKey,
                \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED =>
                    strtr(base64_encode($url . 'for_uenc'), '+/=', '-_,')
            ]
        ];

        $contextMock = $this->getMock('Magento\App\Helper\Context', [], [], '', false);
        $urlBuilderMock = $this->getMockForAbstractClass(
            'Magento\UrlInterface',
            [],
            '',
            true,
            true,
            true,
            ['getCurrentUrl']
        );
        $formKeyMock = $this->getMock('Magento\Data\Form\FormKey', ['getFormKey'], [], '', false);

        $formKeyMock->expects($this->once())
            ->method('getFormKey')
            ->will($this->returnValue($formKey));
        $urlBuilderMock->expects($this->once())
            ->method('getCurrentUrl')
            ->will($this->returnValue($url . 'for_uenc'));

        $model = new \Magento\Core\Helper\PostData($contextMock, $urlBuilderMock, $formKeyMock);

        $actual = $model->getPostData($url, $product);
        $this->assertEquals($expectedArray, $actual);
    }
} 