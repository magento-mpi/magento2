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
        $url = $this->getMock('Magento\Framework\Url', array('getUrl'), array(), '', false);
        $url->expects(
            $this->once()
        )->method(
            'getUrl'
        )->with(
            $this->equalTo('url_prefix/authenticationstart'),
            $this->equalTo(array('_secure' => true, '_current' => false, 'form_key' => false, 'isIframe' => true))
        )->will(
            $this->returnValue('some value')
        );

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Centinel\Model\Service $model */
        $model = $helper->getObject(
            'Magento\Centinel\Model\Service',
            array('url' => $url, 'urlPrefix' => 'url_prefix/')
        );
        $this->assertEquals('some value', $model->getAuthenticationStartUrl());
    }

    public function testLookup()
    {
        $centinelSession = $this->getMock(
            'Magento\Framework\Session\SessionManager',
            array('setData', 'getData'),
            array(),
            '',
            false
        );
        $centinelSession->expects($this->once())->method('setData')->with(array());
        $centinelSession->expects($this->once())->method('getData')->will($this->returnValue('cardType'));

        $api = $this->getMock(
            'Magento\Centinel\Model\Api',
            array(
                'setProcessorId',
                'setMerchantId',
                'setTransactionPwd',
                'setIsTestMode',
                'setDebugFlag',
                'callLookup'
            ),
            array(),
            '',
            false
        );
        $api->expects($this->once())->method('setProcessorId')->will($this->returnValue($api));
        $api->expects($this->once())->method('setMerchantId')->will($this->returnValue($api));
        $api->expects($this->once())->method('setTransactionPwd')->will($this->returnValue($api));
        $api->expects($this->once())->method('setIsTestMode')->will($this->returnValue($api));
        $api->expects($this->once())->method('setDebugFlag')->will($this->returnValue($api));
        $api->expects($this->once())->method('callLookup')->will($this->returnValue('result'));
        $apiFactory = $this->getMock('Magento\Centinel\Model\ApiFactory', array('create'), array(), '', false);
        $apiFactory->expects($this->once())->method('create')->will($this->returnValue($api));

        $state = $this->getMock(
            '\Magento\Centinel\Model\State',
            array('setDataStorage', 'setCardType', 'setChecksum', 'setIsModeStrict', 'setLookupResult'),
            array(),
            '',
            false
        );
        $state->expects(
            $this->any()
        )->method(
            'setDataStorage'
        )->with(
            $centinelSession
        )->will(
            $this->returnValue($state)
        );
        $state->expects($this->once())->method('setCardType')->with('cardType')->will($this->returnValue($state));
        $state->expects($this->once())->method('setChecksum')->will($this->returnValue($state));
        $state->expects($this->once())->method('setLookupResult')->with('result');
        $stateFactory = $this->getMock(
            '\Magento\Centinel\Model\StateFactory',
            array('createState'),
            array(),
            '',
            false
        );
        $stateFactory->expects(
            $this->any()
        )->method(
            'createState'
        )->with(
            'cardType'
        )->will(
            $this->returnValue($state)
        );

        $config = $this->getMock(
            '\Magento\Centinel\Model\Config',
            array('setStore', 'getProcessorId', 'getMerchantId', 'getTransactionPwd', 'getIsTestMode', 'getDebugFlag'),
            array(),
            '',
            false
        );
        $config->expects($this->once())->method('setStore')->will($this->returnValue($config));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Centinel\Model\Service $model */
        $model = $helper->getObject(
            'Magento\Centinel\Model\Service',
            array(
                'apiFactory' => $apiFactory,
                'centinelSession' => $centinelSession,
                'stateFactory' => $stateFactory,
                'config' => $config
            )
        );

        $data = new \Magento\Object(array('card_type' => 'cardType'));

        $model->lookup($data);
    }
}
