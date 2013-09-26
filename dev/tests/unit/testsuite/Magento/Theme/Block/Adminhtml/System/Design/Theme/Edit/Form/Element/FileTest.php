<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form_Element;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHtmlAttributes()
    {
        /** @var $fileBlock \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form_Element_File */
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $collectionFactory = $this->getMock('Magento\Data\Form\Element\CollectionFactory', array(), array(), '', false);

        $fileBlock = $helper->getObject('Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File',
            array('factoryCollection' => $collectionFactory)
        );

        $this->assertContains('accept', $fileBlock->getHtmlAttributes());
        $this->assertContains('multiple', $fileBlock->getHtmlAttributes());
    }
}
