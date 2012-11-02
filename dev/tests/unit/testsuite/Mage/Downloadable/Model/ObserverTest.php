<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Downloadable
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Downloadable_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Downloadable_Model_Observer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Downloadable_Model_Observer();
    }

    public function testPrepareProductSave()
    {
        $downloadableData = array('links' => array());

        $request = $this->getMock('Varien_Object', array('getPost', 'hasIsVirtual'));
        $request->expects($this->once())->method('getPost')->with($this->equalTo('downloadable'))
            ->will($this->returnValue($downloadableData));
        $request->expects($this->any())->method('hasIsVirtual')->will($this->returnValue(true));

        $event = $this->getMock('Varien_Event', array('getRequest', 'getProduct'));
        $event->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $event->expects($this->once())->method('getProduct')->will($this->returnValue($request));

        $observer = $this->getMock('Varien_Event_Observer', array('getEvent'));
        $observer->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($event));

        $this->_model->prepareProductSave($observer);

        $this->assertEquals('downloadable', $request->getTypeId());
        $data = $request->getDownloadableData();
        $this->assertArrayHasKey('links', $data);
        $this->assertEmpty($data['links']);
    }
}
