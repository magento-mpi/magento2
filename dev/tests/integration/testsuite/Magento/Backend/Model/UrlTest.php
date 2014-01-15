<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model;

/**
 * Test class for \Magento\Backend\Model\Url.
 *
 * @magentoAppArea adminhtml
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Model\Url');
    }

    /**
     * @covers \Magento\Backend\Model\Url::isSecure
     */
    public function testIsSecure()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\App\ConfigInterface')
            ->setValue('web/secure/use_in_adminhtml', true);
        $this->assertTrue($this->_model->isSecure());

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\App\ConfigInterface')
            ->setValue('web/secure/use_in_adminhtml', false);
        $this->assertFalse($this->_model->isSecure());

        $this->_model->setData('secure_is_forced', true);
        $this->_model->setData('secure', true);
        $this->assertTrue($this->_model->isSecure());

        $this->_model->setData('secure', false);
        $this->assertFalse($this->_model->isSecure());
    }

    /**
     * @covers \Magento\Backend\Model\Url::setRouteParams
     */
    public function testSetRouteParams()
    {
        $this->_model->setRouteParams(array('_nosecret' => 'any_value'));
        $this->assertTrue($this->_model->getNoSecret());

        $this->_model->setRouteParams(array());
        $this->assertFalse($this->_model->getNoSecret());
    }

    /**
     * App isolation is enabled to protect next tests from polluted registry by getUrl()
     *
     * @magentoAppIsolation enabled
     */
    public function testGetUrl()
    {
        $url = $this->_model->getUrl('adminhtml/auth/login');
        $this->assertContains('admin/auth/login/key/', $url);
    }

    /**
     * @param string $routeName
     * @param string $controller
     * @param string $action
     * @param string $expectedHash
     * @dataProvider getSecretKeyDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetSecretKey($routeName, $controller, $action, $expectedHash)
    {
        /** @var $request \Magento\App\RequestInterface */
        $request = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\App\RequestInterface');
        $request->setControllerName('default_controller')
            ->setActionName('default_action')
            ->setRouteName('default_router');

        $this->_model->setRequest($request);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Session\SessionManagerInterface')
            ->setData('_form_key', 'salt');
        $this->assertEquals($expectedHash, $this->_model->getSecretKey($routeName, $controller, $action));
    }

    /**
     * @return array
     */
    public function getSecretKeyDataProvider()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var $encryptor \Magento\Encryption\EncryptorInterface */
        $encryptor = $objectManager->get('Magento\Encryption\EncryptorInterface');

        return array(
            array('', '', '',
                $encryptor->getHash('default_router' . 'default_controller' . 'default_action' . 'salt')),
            array('', '', 'action',
                $encryptor->getHash('default_router' . 'default_controller' . 'action' . 'salt')),
            array('', 'controller', '',
                $encryptor->getHash('default_router' . 'controller' . 'default_action' . 'salt')),
            array('', 'controller', 'action',
                $encryptor->getHash('default_router' . 'controller' . 'action' . 'salt')),
            array('adminhtml', '', '',
                $encryptor->getHash('adminhtml' . 'default_controller' . 'default_action' . 'salt')),
            array('adminhtml', '', 'action',
                $encryptor->getHash('adminhtml' . 'default_controller' . 'action' . 'salt')),
            array('adminhtml', 'controller', '',
                $encryptor->getHash('adminhtml' . 'controller' . 'default_action' . 'salt')),
            array('adminhtml', 'controller', 'action',
                $encryptor->getHash('adminhtml' . 'controller' . 'action' . 'salt')),
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSecretKeyForwarded()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var $encryptor \Magento\Encryption\EncryptorInterface */
        $encryptor = $objectManager->get('Magento\Encryption\EncryptorInterface');

        /** @var $request \Magento\App\Request\Http */
        $request = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\App\RequestInterface');
        $request->setControllerName('controller')->setActionName('action');
        $request->initForward()->setControllerName(uniqid())->setActionName(uniqid());
        $this->_model->setRequest($request);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Session\SessionManagerInterface')
            ->setData('_form_key', 'salt');
        $this->assertEquals(
            $encryptor->getHash('controller' . 'action' . 'salt'),
            $this->_model->getSecretKey()
        );
    }

    public function testUseSecretKey()
    {
        $this->_model->setNoSecret(true);
        $this->assertFalse($this->_model->useSecretKey());

        $this->_model->setNoSecret(false);
        $this->assertTrue($this->_model->useSecretKey());
    }
}
