<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Newsletter_Model_TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param bool $isSingleStore
     * @dataProvider getProcessedTemplateDataProvider
     */
    public function testGetProcessedTemplate($isSingleStore)
    {
        $design = $this->getMock('Magento_Core_Model_View_DesignInterface');
        $context = $this->getMock('Magento_Core_Model_Context', array(), array(), '', false);
        $registry = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false);

        $storeManager = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);
        $storeManager->expects($this->once())
            ->method('hasSingleStore')
            ->will($this->returnValue($isSingleStore));

        $request = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);

        if ($isSingleStore) {
            $store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
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

        $filter = $this->getMock('Magento_Newsletter_Model_Template_Filter', array(), array(), '', false);
        $appEmulation = $this->getMock('Magento_Core_Model_App_Emulation $appEmulation', array(), array(), '', false);
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

        $storeConfig = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $templateFactory = $this->getMock('Magento_Newsletter_Model_TemplateFactory');
        $data = array('template_text' => 'template text');

        /** @var Magento_Newsletter_Model_Template $model */
        $model = $this->getMock('Magento_Newsletter_Model_Template', array('_init'), array(
            $design, $context, $registry, $storeManager, $request, $filter, $storeConfig, $templateFactory,
            $appEmulation, $data,
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
