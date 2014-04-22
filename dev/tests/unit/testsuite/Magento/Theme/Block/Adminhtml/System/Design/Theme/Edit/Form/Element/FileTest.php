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
namespace Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form\Element;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHtmlAttributes()
    {
        /** @var $fileBlock \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form\Element\File */
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $collectionFactory = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            array(),
            array(),
            '',
            false
        );

        $fileBlock = $helper->getObject(
            'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form\Element\File',
            array('factoryCollection' => $collectionFactory)
        );

        $this->assertContains('accept', $fileBlock->getHtmlAttributes());
        $this->assertContains('multiple', $fileBlock->getHtmlAttributes());
    }
}
