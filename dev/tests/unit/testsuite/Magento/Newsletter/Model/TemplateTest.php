<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Model;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param bool $isSingleStore
     * @dataProvider getProcessedTemplateDataProvider
     */
    public function testGetProcessedTemplate($isSingleStore)
    {
        $design = $this->getMock('Magento\View\DesignInterface');
        $context = $this->getMock('Magento\Model\Context', array(), array(), '', false);
        $registry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);

        $storeManager = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);
        $storeManager->expects($this->once())
            ->method('hasSingleStore')
            ->will($this->returnValue($isSingleStore));

        $request = $this->getMock('Magento\App\RequestInterface', array(), array(), '', false);

        if ($isSingleStore) {
            $store = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);
            $store->expects($this->once())
                ->method('getId')
                ->will($this->returnValue('test_id'));

            $storeManager->expects($this->once())
                ->method('getStore')
                ->will($this->returnValue($store));
        } else {
            $request->expects($this->once())
                ->method('getParam')
                ->with('store_id')
                ->will($this->returnValue('test_id'));
        }

        $filter = $this->getMock('Magento\Newsletter\Model\Template\Filter', array(), array(), '', false);
        $appEmulation = $this->getMock('Magento\Core\Model\App\Emulation', array(), array(), '', false);
        $filter->expects($this->once())
            ->method('setStoreId')
            ->with('test_id');
        $filter->expects($this->once())
            ->method('setIncludeProcessor')
            ->will($this->returnSelf());
        $filter->expects($this->once())
            ->method('filter')
            ->with('template text')
            ->will($this->returnValue('processed text'));

        $storeConfig = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $templateFactory = $this->getMock('Magento\Newsletter\Model\TemplateFactory');
        $data = array('template_text' => 'template text');

        $filterManager = $this->getMock('\Magento\Filter\FilterManager', array(), array(), '', false);

        /** @var \Magento\Newsletter\Model\Template $model */
        $model = $this->getMock('Magento\Newsletter\Model\Template', array('_init'), array(
            $context, $design, $registry, $appEmulation, $storeManager, $request, $filter, $storeConfig,
            $templateFactory, $filterManager, $data,
        ));

        $result = $model->getProcessedTemplate();
        $this->assertEquals('processed text', $result);
    }

    /**
     * @return array
     */
    public static function getProcessedTemplateDataProvider()
    {
        return array(
            'single store' => array(true),
            'multi store' => array(false),
        );
    }
}
