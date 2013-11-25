<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Centinel\Model\Service
 */
namespace Magento\Centinel\Model;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Centinel\Model\Service::getAuthenticationStartUrl
     * @covers \Magento\Centinel\Model\Service::_getUrl
     */
    public function testGetAuthenticationStartUrl()
    {
        $url = $this->getMock('Magento\Core\Model\Url', array('getUrl'), array(), '', false);
        $url->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('url_prefix/authenticationstart'))
            ->will($this->returnValue('some value'));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var Service $model */
        $model = $helper->getObject(
            'Magento\Centinel\Model\Service',
            array('url' => $url, 'urlPrefix' => 'url_prefix/')
        );
        $this->assertEquals('some value', $model->getAuthenticationStartUrl());
    }
}
