<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Session;

class SidResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Session\SidResolver
     */
    protected $model;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $session;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    protected $customSessionName = 'csn';

    /**
     * @var string
     */
    protected $customSessionQueryParam = 'csqp';

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Framework\Session\Generic _model */
        $this->session = $objectManager->get('Magento\Framework\Session\Generic');

        $this->scopeConfig = $this->getMockBuilder(
            'Magento\Framework\App\Config\ScopeConfigInterface'
        )->setMethods(
            array('getValue')
        )->disableOriginalConstructor()->getMockForAbstractClass();

        $this->urlBuilder = $this->getMockBuilder(
            'Magento\Framework\Url'
        )->setMethods(
            array('isOwnOriginUrl')
        )->disableOriginalConstructor()->getMockForAbstractClass();

        $this->model = $objectManager->create(
            'Magento\Framework\Session\SidResolver',
            array(
                'scopeConfig' => $this->scopeConfig,
                'urlBuilder' => $this->urlBuilder,
                'sidNameMap' => array($this->customSessionName => $this->customSessionQueryParam)
            )
        );
    }

    public function tearDown()
    {
        if (is_object($this->model) && isset($_GET[$this->model->getSessionIdQueryParam($this->session)])) {
            unset($_GET[$this->model->getSessionIdQueryParam($this->session)]);
        }
    }

    /**
     * @param mixed $sid
     * @param bool $useFrontedSid
     * @param bool $isOwnOriginUrl
     * @param mixed $testSid
     * @dataProvider dataProviderTestGetSid
     */
    public function testGetSid($sid, $useFrontedSid, $isOwnOriginUrl, $testSid)
    {
        $this->scopeConfig->expects(
            $this->any()
        )->method(
            'getValue'
        )->with(
            \Magento\Framework\Session\SidResolver::XML_PATH_USE_FRONTEND_SID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->will(
            $this->returnValue($useFrontedSid)
        );

        $this->urlBuilder->expects($this->any())->method('isOwnOriginUrl')->will($this->returnValue($isOwnOriginUrl));

        if ($testSid) {
            $_GET[$this->model->getSessionIdQueryParam($this->session)] = $testSid;
        }
        $this->assertEquals($sid, $this->model->getSid($this->session));
    }

    /**
     * @return array
     */
    public function dataProviderTestGetSid()
    {
        return array(
            array(null, false, false, 'test-sid'),
            array(null, false, true, 'test-sid'),
            array(null, false, false, 'test-sid'),
            array(null, true, false, 'test-sid'),
            array(null, false, true, 'test-sid'),
            array('test-sid', true, true, 'test-sid'),
            array(null, true, true, null)
        );
    }

    public function testGetSessionIdQueryParam()
    {
        $this->assertEquals(SidResolver::SESSION_ID_QUERY_PARAM, $this->model->getSessionIdQueryParam($this->session));
    }

    public function testGetSessionIdQueryParamCustom()
    {
        $oldSessionName = $this->session->getName();
        $this->session->setName($this->customSessionName);
        $this->assertEquals($this->customSessionQueryParam, $this->model->getSessionIdQueryParam($this->session));
        $this->session->setName($oldSessionName);
    }

    public function testSetGetUseSessionVar()
    {
        $this->assertFalse($this->model->getUseSessionVar());
        $this->model->setUseSessionVar(true);
        $this->assertTrue($this->model->getUseSessionVar());
    }

    public function testSetGetUseSessionInUrl()
    {
        $this->assertTrue($this->model->getUseSessionInUrl());
        $this->model->setUseSessionInUrl(false);
        $this->assertFalse($this->model->getUseSessionInUrl());
    }
}
