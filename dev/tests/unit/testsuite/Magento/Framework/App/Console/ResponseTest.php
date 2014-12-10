<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
    }

    public function testSendResponseDefaultBehaviour()
    {
        $this->model->terminateOnSend(false);
        $this->assertEquals(0, $this->model->sendResponse());
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
        return [[$largeCode, 255], [$lowCode, $lowCode], [$lowestCode, $lowestCode]];
    }
}
