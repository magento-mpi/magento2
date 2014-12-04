<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Console;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Console\Response
     */
    protected $model;

    public function setUp()
    {
        $this->model = new \Magento\Framework\App\Console\Response();
        $this->model->terminateOnSend(false);
    }

    public function testSendResponseDefaultBehaviour()
    {
        $this->assertEquals(0, $this->model->sendResponse());
    }

    /**
     * @dataProvider setCodeProvider
     */
    public function testSetCode($code, $expectedCode)
    {
        $this->model->setCode($code);
        $result = $this->model->sendResponse();
        $this->assertEquals($expectedCode, $result);
    }

    public static function setCodeProvider()
    {
        $largeCode = 256;
        $lowCode = 1;
        $lowestCode = -255;
        return array(array($largeCode, 255), array($lowCode, $lowCode), array($lowestCode, $lowestCode));
    }

    public function testSetBody()
    {
        $output = 'output';
        $this->expectOutputString($output);
        $this->model->setBody($output);
        $this->model->sendResponse();
    }

    public function testSetBodyNoOutput()
    {
        $this->expectOutputString('');
        $this->model->sendResponse();
    }
}
