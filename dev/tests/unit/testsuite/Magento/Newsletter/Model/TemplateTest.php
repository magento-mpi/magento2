<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Newsletter_Model_TemplateTest extends PHPUnit_Framework_TestCase
{
    public function testGetProcessedTemplate()
    {
        $design = $this->getMock('Magento_Core_Model_View_DesignInterface');
        $context = $this->getMock('Magento_Core_Model_Context', array(), array(), '', false);
        $registry = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false);

        $store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $store->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('test_id'));
        $storeManager = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);
        $storeManager->expects($this->once())
            ->method('hasSingleStore')
            ->will($this->returnValue(true));
        $storeManager->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));

        $filter = $this->getMock('Magento_Newsletter_Model_Template_Filter', array(), array(), '', false);
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

        $data = array('template_text' => 'template text');

        $model = $this->getMock('Magento_Newsletter_Model_Template', array('_init'),
            array($design, $context, $registry, $storeManager, $filter, $data));

        $result = $model->getProcessedTemplate();
        $this->assertEquals('processed text', $result);
    }
}
