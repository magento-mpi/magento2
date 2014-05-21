<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Message;

/**
 * \Magento\Framework\Message\Notice test case
 */
class NoticeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Message\Notice
     */
    protected $model;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Framework\Message\Notice');
    }

    public function testGetType()
    {
        $this->assertEquals(MessageInterface::TYPE_NOTICE, $this->model->getType());
    }
}
