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
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetRepo;

    /**
     * @var Review
     */
    protected $model;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->assetRepo = $this->getMock('Magento\Framework\View\Asset\Repository', [], [], '', false);
        $this->model = $helper->getObject(
            'Magento\Paypal\Block\Express\Review',
            ['request' => $this->request, 'assetRepo' => $this->assetRepo]
        );
    }

    /**
     * @param bool $isSecure
     * @dataProvider getViewFileUrlDataProvider
     */
    public function testGetViewFileUrl($isSecure)
    {
        $this->request->expects($this->once())->method('isSecure')->will($this->returnValue($isSecure));
        $this->assetRepo->expects($this->once())
            ->method('getUrlWithParams')
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
