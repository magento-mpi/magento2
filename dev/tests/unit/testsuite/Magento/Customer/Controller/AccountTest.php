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
     * @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
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
            'Magento\App\RequestInterface',
            ['isPost', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam'],
            [],
            '',
            false
        );
        $this->response = $this->getMock(
            'Magento\App\ResponseInterface',
            ['setRedirect', 'sendResponse'],
            [],
            '',
            false
        );
        $this->customerSession = $this->getMock(
            '\Magento\Customer\Model\Session',
            ['isLoggedIn', 'getLastCustomerId', 'getBeforeAuthUrl', 'setBeforeAuthUrl'],
            [],
            '',
            false
        );
        $this->url = $this->getMockForAbstractClass('\Magento\UrlInterface');
        $this->objectManager = $this->getMock('\Magento\ObjectManager\ObjectManager', ['get'], [], '', false);
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_formKeyValidator = $this->getMock(
            'Magento\Core\App\Action\FormKeyValidator', array(), array(), '', false
        );
        $this->object = $objectManager->getObject('Magento\Customer\Controller\Account', [
            'request' => $this->request,
            'response' => $this->response,
            'customerSession' => $this->customerSession,
            'url' => $this->url,
            'objectManager' => $this->objectManager,
            'formKeyValidator' => $this->_formKeyValidator,
            ''
        ]);
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
        $this->objectManager->expects($this->any())->method('get')
            ->will($this->returnValueMap([
                ['Magento\Customer\Helper\Data', new \Magento\Object(['account_url' => 1])],
                ['Magento\Store\Model\Store\Config', new \Magento\Object(['config_flag' => 1])],
                ['Magento\Core\Helper\Data', $this->getMock('Magento\Core\Helper\Data', [], [], '', false)],
            ]));
        $this->customerSession->expects($this->at(0))->method('isLoggedIn')->with()->will($this->returnValue(0));
        $this->customerSession->expects($this->at(4))->method('isLoggedIn')->with()->will($this->returnValue(1));
        $this->request->expects($this->once())->method('getParam')
            ->with(\Magento\Customer\Helper\Data::REFERER_QUERY_PARAM_NAME)
            ->will($this->returnValue('referer'));
        $this->url->expects($this->once())->method('isOwnOriginUrl')->with();

        $this->object->loginPostAction();
    }
}
