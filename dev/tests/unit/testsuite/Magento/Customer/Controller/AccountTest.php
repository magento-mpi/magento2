<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test customer account controller
 */
namespace Magento\Customer\Controller;

class AccountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Controller\Account
     */
    protected $object;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSession;

    /**
     * @var \Magento\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $url;

    /**
     * @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * List of actions that are allowed for not authorized users
     *
     * @var array
     */
    protected $openActions = array(
        'create',
        'login',
        'logoutsuccess',
        'forgotpassword',
        'forgotpasswordpost',
        'resetpassword',
        'resetpasswordpost',
        'confirm',
        'confirmation',
        'createpassword',
        'createpost',
        'loginpost'
    );

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_formKeyValidator;

    protected function setUp()
    {
        $this->request = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            array('isPost', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam'),
            array(),
            '',
            false
        );
        $this->response = $this->getMock(
            'Magento\Framework\App\ResponseInterface',
            array('setRedirect', 'sendResponse'),
            array(),
            '',
            false
        );
        $this->customerSession = $this->getMock(
            '\Magento\Customer\Model\Session',
            array('isLoggedIn', 'getLastCustomerId', 'getBeforeAuthUrl', 'setBeforeAuthUrl'),
            array(),
            '',
            false
        );
        $this->url = $this->getMockForAbstractClass('\Magento\UrlInterface');
        $this->objectManager = $this->getMock(
            '\Magento\ObjectManager\ObjectManager',
            array('get'),
            array(),
            '',
            false
        );
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_formKeyValidator = $this->getMock(
            'Magento\Core\App\Action\FormKeyValidator',
            array(),
            array(),
            '',
            false
        );
        $this->object = $objectManager->getObject(
            'Magento\Customer\Controller\Account',
            array(
                'request' => $this->request,
                'response' => $this->response,
                'customerSession' => $this->customerSession,
                'url' => $this->url,
                'objectManager' => $this->objectManager,
                'formKeyValidator' => $this->_formKeyValidator,
                ''
            )
        );
    }

    /**
     * @covers \Magento\Customer\Controller\Account::_getAllowedActions
     */
    public function testGetAllowedActions()
    {
        $this->assertAttributeEquals($this->openActions, '_openActions', $this->object);
        /**
         * @TODO: [TD] Protected methods must be tested via public. Eliminate _getAllowedActions method and write test
         *   for dispatch method using this property instead.
         */
        $method = new \ReflectionMethod('Magento\Customer\Controller\Account', '_getAllowedActions');
        $method->setAccessible(true);
        $this->assertEquals($this->openActions, $method->invoke($this->object));
    }

    public function testLoginPostActionWhenRefererSetBeforeAuthUrl()
    {
        $this->_formKeyValidator->expects($this->once())->method('validate')->will($this->returnValue(true));
        $this->objectManager->expects(
            $this->any()
        )->method(
            'get'
        )->will(
            $this->returnValueMap(
                array(
                    array('Magento\Customer\Helper\Data', new \Magento\Object(array('account_url' => 1))),
                    array('Magento\Framework\App\Config\ScopeConfigInterface', new \Magento\Object(array('config_flag' => 1))),
                    array(
                        'Magento\Core\Helper\Data',
                        $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false)
                    )
                )
            )
        );
        $this->customerSession->expects($this->at(0))->method('isLoggedIn')->with()->will($this->returnValue(0));
        $this->customerSession->expects($this->at(4))->method('isLoggedIn')->with()->will($this->returnValue(1));
        $this->request->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            \Magento\Customer\Helper\Data::REFERER_QUERY_PARAM_NAME
        )->will(
            $this->returnValue('referer')
        );
        $this->url->expects($this->once())->method('isOwnOriginUrl')->with();

        $this->object->loginPostAction();
    }
}
