<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_AbstractTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSampleCollection
     */
    public function testGetListItems($collection)
    {
        /** @var $listAbstractBlock Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract */
        $listAbstractBlock = $this->getMockForAbstractClass(
            'Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract', array(), '', false, false, true,
            array('getChildBlock')
        );

        $itemBlock =  $this->getMock('Mage_DesignEditor_Block_Adminhtml_Theme_Item', array(), array(), '', false);

        $listAbstractBlock->setCollection($collection);

        $listAbstractBlock->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->will($this->returnValue($itemBlock));

        $this->assertEquals(2, count($listAbstractBlock->getListItems()));
    }

    /**
     * @return array
     */
    public function getSampleCollection()
    {
        return array(array(array(
            array('first_item'),
            array('second_item')
        )));
    }
}
