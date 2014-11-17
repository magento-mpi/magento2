<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Backend\App;

class UserConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $response;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockConfig;

    protected function setUp()
    {
        $this->factory = $this->getMock('Magento\Backend\Model\Config\Factory', [], [], '', false);
        $this->response = $this->getMock('Magento\Framework\App\Console\Response', [], [], '', false);
        $this->mockConfig = $this->getMock('Magento\Backend\Model\Config', [], [], '', false);
    }

    public function testUserRequestCreation()
    {
        $key = 'key';
        $value = 'value';
        $request = [
            $key => $value
        ];
        $model = $this->createModel($request);
        $this->factory->expects(
            $this->once()
        )->method(
                'create'
            )->will(
                $this->returnValue($this->mockConfig)
            );
        $this->mockConfig->expects(
            $this->once()
        )->method(
                'setDataByPath'
            )->with(
                $key,
                $value
            );
        $this->mockConfig->expects(
            $this->once()
        )->method(
                'save'
            );

        $model->launch();
    }

    /**
     * Creates the model with mocked dependencies
     *
     * @param array $request
     * @return UserConfig
     */
    private function createModel($request)
    {
        return new UserConfig($this->factory, $this->response, $request);
    }
}
