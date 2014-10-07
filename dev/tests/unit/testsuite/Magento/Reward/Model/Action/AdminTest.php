<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Action;

class AdminTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Action\Admin
     */
    protected $model;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject('\Magento\Reward\Model\Action\Admin');
    }

    public function testCanAddRewardPoints()
    {
        $this->assertTrue($this->model->canAddRewardPoints());
    }

    public function testGetHistoryMessage()
    {
        $this->assertEquals('Updated by moderator', $this->model->getHistoryMessage());
    }
}
