<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Block\Express;

class ReviewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\View\Asset\Service|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetService;

    /**
     * @var Review
     */
    protected $model;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->request = $this->getMock('Magento\App\Request\Http', [], [], '', false);
        $this->assetService = $this->getMock('Magento\View\Asset\Service', [], [], '', false);
        $this->model = $helper->getObject(
            'Magento\Paypal\Block\Express\Review',
            ['request' => $this->request, 'assetService' => $this->assetService]
        );
    }

    /**
     * @param bool $isSecure
     * @dataProvider getViewFileUrlDataProvider
     */
    public function testGetViewFileUrl($isSecure)
    {
        $this->request->expects($this->once())->method('isSecure')->will($this->returnValue($isSecure));
        $this->assetService->expects($this->once())
            ->method('getAssetUrlWithParams')
            ->with('some file', $this->callback(function ($value) use ($isSecure) {
                return isset($value['_secure']) && $value['_secure'] === $isSecure;
            }))
            ->will($this->returnValue('result url'));
        $this->assertEquals('result url', $this->model->getViewFileUrl('some file'));
    }

    public function getViewFileUrlDataProvider()
    {
        return [[true], [false]];
    }
}
