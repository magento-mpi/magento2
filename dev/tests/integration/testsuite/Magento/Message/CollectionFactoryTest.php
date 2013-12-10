<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * \Magento\Message\CollectionFactory test case
 */
class CollectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Message\CollectionFactory
     */
    protected $model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create('Magento\Message\CollectionFactory');
    }

    public function testCreate()
    {
        $message = $this->model->create();
        $this->assertInstanceOf('\Magento\Message\Collection', $message);
    }
}
