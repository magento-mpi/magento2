<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wrapper to pass method calls and arguments to mockup inside it
 */
namespace Magento\Core\Model\Route;

class Wrapper extends \PHPUnit_Framework_TestCase implements \Magento\Framework\Config\CacheInterface
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mock;

    public function __construct()
    {
        $this->_mock = $this->getMock('SomeClass', array('get', 'put'));
    }

    public function getRealMock()
    {
        return $this->_mock;
    }

    public function get($areaCode, $cacheId)
    {
        return $this->_mock->get($areaCode, $cacheId);
    }

    public function put($routes, $areaCode, $cacheId)
    {
        return $this->_mock->put($routes, $areaCode, $cacheId);
    }
}
