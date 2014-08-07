<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sendfriend\Model;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test Sendfriend
 *
 */
class SendfriendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sendfriend\Model\Sendfriend
     */
    protected $model;

    /**
     * @var \Magento\Framework\Stdlib\CookieManager
     */
    protected $cookieManager;

    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->model = $objectManager->create('Magento\Sendfriend\Model\Sendfriend');
        $this->cookieManager = $objectManager->create('\Magento\Framework\Stdlib\CookieManager');
    }

    public function testSetAndGetCookieManager()
    {
        $this->model->setCookieManager($this->cookieManager);
        $result = $this->model->getCookieManager();
        $this->assertSame($this->cookieManager, $result);
    }

    public function testGetCookieManagerWithException()
    {
        $this->model->setCookieManager(null);
        try {
            $this->model->getCookieManager();
            $this->fail('Failed to model exception');
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->assertEquals(
                'Please define a correct CookieManager instance.',
                $e->getMessage()
            );
        }
    }
}
