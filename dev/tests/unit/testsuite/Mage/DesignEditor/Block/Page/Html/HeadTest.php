<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Page_Html_HeadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Block_Page_Html_Head
     */
    protected $_model;

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @param array $sourceItems
     * @param array $vdeItems
     * @param array $expectedItems
     *
     * @dataProvider getCssJsHtmlDataProvider
     */
    public function testGetCssJsHtml(array $sourceItems, array $vdeItems = null, array $expectedItems = null)
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);
        if ($vdeItems !== null) {
            /** @var $vdeHead Mage_DesignEditor_Block_Page_Html_Head_Vde */
            $vdeHead = $helper->getBlock('Mage_DesignEditor_Block_Page_Html_Head_Vde');
            $vdeHead->setData('items', $vdeItems);

            $layoutMock = $this->getMock('Mage_Core_Model_Layout', array('getBlock'), array(), '', false);
            $layoutMock->expects($this->once())
                ->method('getBlock')
                ->will($this->returnValue($vdeHead));

            $this->_model = $helper->getBlock(
                'Mage_DesignEditor_Block_Page_Html_Head',
                array('layout' => $layoutMock)
            );
        } else {
            $this->_model = $helper->getBlock('Mage_DesignEditor_Block_Page_Html_Head');
        }
        $this->_model->setData('items', $sourceItems);

        $this->assertInternalType('string', $this->_model->getCssJsHtml());
        $this->assertEquals($expectedItems, $this->_model->getData('items'));
    }

    /**
     * Data provider for testGetCssJsHtml
     *
     * @return array
     */
    public function getCssJsHtmlDataProvider()
    {
        $jsSourceItems = array(
            'js/first'  => 'js first',
            'js/second' => 'js second'
        );
        $cssSourceItems = array(
            'css/first' => 'css first'
        );
        $sourceItems = array_merge($jsSourceItems, $cssSourceItems);

        $vdeItems = array(
            'js/first'   => 'js first vde',
            'css/second' => 'css second',
        );

        return array(
            'no block' => array(
                '$sourceItems'   => $sourceItems,
                '$vdeItems'      => null,
                '$expectedItems' => $sourceItems,
            ),
            'no vde data' => array(
                '$sourceItems'   => $sourceItems,
                '$vdeItems'      => array(),
                '$expectedItems' => $cssSourceItems,
            ),
            'item merge' => array(
                '$sourceItems'   => $sourceItems,
                '$vdeItems'      => $vdeItems,
                '$expectedItems' => array_merge($cssSourceItems, $vdeItems),
            ),
        );
    }
}
