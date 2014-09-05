<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Model\Cart\Access;

class WritePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Checkout\Model\Cart\Access\WritePlugin
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userContextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->userContextMock = $this->getMock('Magento\Authorization\Model\UserContextInterface');
        $this->subjectMock = $this->getMock('\Magento\Checkout\Service\V1\Cart\WriteServiceInterface');

        $this->model = new WritePlugin($this->userContextMock);
    }

    /**
     * @param $userType
     * @dataProvider successTypeDataProvider
     */
    public function testBeforeCreateSuccess($userType)
    {
        $this->userContextMock->expects($this->once())->method('getUserType')->will($this->returnValue($userType));
        $this->model->beforeCreate($this->subjectMock);
    }

    public function successTypeDataProvider()
    {
        return [
            'admin' => [\Magento\Authorization\Model\UserContextInterface::USER_TYPE_ADMIN],
            'guest' => [\Magento\Authorization\Model\UserContextInterface::USER_TYPE_GUEST],
            'integration' => [\Magento\Authorization\Model\UserContextInterface::USER_TYPE_INTEGRATION],
        ];
    }

    /**
     * @expectedException \Magento\Framework\Exception\AuthorizationException
     * @expectedExceptionMessage Access denied
     */
    public function testBeforeCreateDenied()
    {
        $this->userContextMock->expects($this->once())->method('getUserType')
            ->will($this->returnValue(\Magento\Authorization\Model\UserContextInterface::USER_TYPE_CUSTOMER));
        $this->model->beforeCreate($this->subjectMock);
    }
}
