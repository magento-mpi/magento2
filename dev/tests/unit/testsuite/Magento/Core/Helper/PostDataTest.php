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
        $product = array('product' => new \Magento\Object(array('id' => 1)));
        $expected = json_encode(
            array(
                'action' => $url,
                'data' => array(
                    'product' => new \Magento\Object(array('id' => 1)),
                    'form_key' => $formKey,
                    \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED => strtr(
                        base64_encode($url . 'for_uenc'),
                        '+/=',
                        '-_,'
                    )
                )
            )
        );

        $contextMock = $this->getMock('Magento\App\Helper\Context', array('getUrlBuilder'), array(), '', false);
        $urlBuilderMock = $this->getMockForAbstractClass(
            'Magento\UrlInterface',
            array(),
            '',
            true,
            true,
            true,
            array('getCurrentUrl')
        );
        $formKeyMock = $this->getMock('Magento\Data\Form\FormKey', array('getFormKey'), array(), '', false);

        $contextMock->expects($this->once())->method('getUrlBuilder')->will($this->returnValue($urlBuilderMock));
        $formKeyMock->expects($this->once())->method('getFormKey')->will($this->returnValue($formKey));
        $urlBuilderMock->expects($this->once())->method('getCurrentUrl')->will($this->returnValue($url . 'for_uenc'));

        $model = new \Magento\Core\Helper\PostData($contextMock, $formKeyMock);

        $actual = $model->getPostData($url, $product);
        $this->assertEquals($expected, $actual);
    }
}
