<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * \Magento\Message\Success test case
 */
class SuccessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Message\Success
     */
    protected $model;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Message\Success');
    }

    public function testGetType()
    {
        $this->assertEquals(MessageInterface::TYPE_SUCCESS, $this->model->getType());
    }
}
