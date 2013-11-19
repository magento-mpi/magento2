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

class AbstractSessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Session\AbstractSession
     */
    protected $_model;

    /**
     * @var \Magento\Session\SidResolverInterface
     */
    protected $_sidResolver;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Session\SidResolverInterface $sidResolver */
        $this->_sidResolver = $objectManager->get('Magento\Session\SidResolverInterface');

        /** @var \Magento\Core\Model\Session\AbstractSession _model */
        $this->_model = $this->getMockForAbstractClass(
            'Magento\Core\Model\Session\AbstractSession',
            array($objectManager->create(
                'Magento\Core\Model\Session\Context',
                array('sidResolver' => $this->_sidResolver)
            ))
        );
    }

    public function testInit()
    {
        $this->_model->init('test');
        $this->_model->setTestData('test');
        $data = $this->_model->getData();
        $this->assertArrayHasKey('test_data', $data);
        $this->assertSame($_SESSION['test'], $data);
    }

    public function testGetData()
    {
        $this->_model->setData(array('test_key' => 'test_value'));
        $this->assertEquals('test_value', $this->_model->getData('test_key', true));
        $this->assertNull($this->_model->getData('test_key'));
    }

    public function testGetSessionId()
    {
        $this->assertEquals(session_id(), $this->_model->getSessionId());
    }

    public function testGetName()
    {
        $this->assertEquals(session_name(), $this->_model->getName());
    }

    public function testSetSessionName()
    {
        $this->_model->setSessionName('test');
        $this->assertEquals('test', $this->_model->getName());
    }

    public function testUnsetAll()
    {
        $data = array('key' => 'value');
        $this->_model->setData($data);

        $this->assertEquals($data, $this->_model->getData());
        $this->_model->unsetAll();

        $this->assertEquals(array(), $this->_model->getData());
    }

    public function testSetSessionId()
    {
        $sessionId = $this->_model->getSessionId();
        $this->_model->setSessionId($this->_sidResolver->getSid($this->_model));
        $this->assertEquals($sessionId, $this->_model->getSessionId());

        $this->_model->setSessionId('test');
        $this->assertEquals('test', $this->_model->getSessionId());
    }

    /**
     * @magentoConfigFixture current_store web/session/use_frontend_sid 1
     */
    public function testSetSessionIdFromParam()
    {
        $this->assertNotEquals('test_id', $this->_model->getSessionId());
        $_GET[$this->_sidResolver->getSessionIdQueryParam($this->_model)] = 'test-id';
        $this->_model->setSessionId($this->_sidResolver->getSid($this->_model));

        $this->assertEquals('test-id', $this->_model->getSessionId());

        /* Use not valid identifier */
        $_GET[$this->_sidResolver->getSessionIdQueryParam($this->_model)] = 'test_id';
        $this->_model->setSessionId($this->_sidResolver->getSid($this->_model));
        $this->assertEquals('test-id', $this->_model->getSessionId());
    }


    public function testGetSessionIdForHost()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $this->_model->init('test');
        $this->assertEmpty($this->_model->getSessionIdForHost('localhost'));
        $this->assertNotEmpty($this->_model->getSessionIdForHost('test'));
    }

    public function testIsValidForHost()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $this->_model->init('test');
        $this->assertFalse($this->_model->isValidForHost('test.com'));
        $this->assertTrue($this->_model->isValidForHost('localhost'));
    }

    public function testGetSessionSaveMethod()
    {
        $this->assertEquals('files', $this->_model->getSessionSaveMethod());
    }

    public function testGetSessionSavePath()
    {
        $this->assertEquals(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Dir')
                ->getDir('session'),
            $this->_model->getSessionSavePath()
        );
    }
}
