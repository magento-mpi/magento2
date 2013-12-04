<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * \Magento\Message\Warning test case
 */
class WarningTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Message\Warning
     */
    protected $model;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Message\Warning');
    }

    public function testGetType()
    {
        $this->assertEquals(MessageInterface::TYPE_WARNING, $this->model->getType());
    }
}
