<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Console;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Console\Response
     */
    protected $model;

    public function setUp()
    {
        $this->model = new \Magento\App\Console\Response();
    }

    public function testSendResponseDefaultBehaviour()
    {
        $this->model->terminateOnSend(false);
        $this->assertEquals(0 ,$this->model->sendResponse());
    }

    /**
     * @dataProvider setCodeProvider
     */
    public function testSetCode($code, $expectedCode)
    {
        $this->model->terminateOnSend(false);
        $this->model->setCode($code);
        $result = $this->model->sendResponse();
        $this->assertEquals($expectedCode, $result);
    }

    public static function setCodeProvider()
    {
        $largeCode = 256;
        $lowCode = 1;
        $lowestCode = -255;
        return array(
            array($largeCode, 255),
            array($lowCode, $lowCode),
            array($lowestCode, $lowestCode),
        );
    }
}
