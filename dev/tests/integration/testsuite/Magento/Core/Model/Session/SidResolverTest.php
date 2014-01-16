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

namespace Magento\Core\Model\Session;

class SidResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Session\SidResolver
     */
    protected $model;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $session;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Store
     */
    protected $store;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Store\ConfigInterface
     */
    protected $coreStoreConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\UrlInterface
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

        /** @var \Magento\Core\Model\Session _model */
        $this->session = $objectManager->get('Magento\Core\Model\Session');

        $this->coreStoreConfig = $this->getMockBuilder('Magento\Core\Model\Store\ConfigInterface')
            ->setMethods(array('getConfig'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->urlBuilder = $this->getMockBuilder('Magento\UrlInterface')
            ->setMethods(array('isOwnOriginUrl'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->model = $objectManager->create(
            'Magento\Core\Model\Session\SidResolver',
            array(
                'coreStoreConfig' => $this->coreStoreConfig,
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
        $this->coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->with(SidResolver::XML_PATH_USE_FRONTEND_SID)
            ->will($this->returnValue($useFrontedSid));

        $this->urlBuilder->expects($this->any())
            ->method('isOwnOriginUrl')
            ->will($this->returnValue($isOwnOriginUrl));

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
            array(null, true, true, null),
        );
    }

    public function testGetSessionIdQueryParam()
    {
        $this->assertEquals(
            SidResolver::SESSION_ID_QUERY_PARAM,
            $this->model->getSessionIdQueryParam($this->session)
        );
    }

    public function testGetSessionIdQueryParamCustom()
    {
        $oldSessionName = $this->session->getName();
        $this->session->setName($this->customSessionName);
        $this->assertEquals(
            $this->customSessionQueryParam,
            $this->model->getSessionIdQueryParam($this->session)
        );
        $this->session->setName($oldSessionName);
    }
}
