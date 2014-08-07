<?php
/**
 * \Magento\Webapi\Controller\Request
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller;

use \Magento\TestFramework\Helper\Bootstrap;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Webapi\Controller\Request
     */
    public $request;

    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->request = $objectManager->get('\Magento\Webapi\Controller\Request');
    }

    public function testConsumerId()
    {
        $consumerId = 99;
        $this->request->setConsumerId($consumerId);
        $this->assertEquals($consumerId, $this->request->getConsumerId());
    }
} 