<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit;

/**
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\View\LayoutInterface */
    protected $_layout = null;

    /** @var \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        $this->_block = $this->_layout->createBlock('Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form');
    }

    public function testGetGridJsObject()
    {
        $parentName = 'parent';
        $mockClass = $this->getMockClass(
            'Magento\Catalog\Block\Product\AbstractProduct',
            array('_prepareLayout'),
            array(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
                    'Magento\Framework\View\Element\Template\Context'
                )
            )
        );
        $this->_layout->createBlock($mockClass, $parentName);
        $this->_layout->setChild($parentName, $this->_block->getNameInLayout(), '');

        $pageGrid = $this->_layout->addBlock(
            'Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form\Grid',
            'cms_page_grid',
            $parentName
        );
        $this->assertEquals($pageGrid->getJsObjectName(), $this->_block->getGridJsObject());
    }

    /**
     * @param int $isMetadataEnabled
     * @param bool $result
     *
     * @dataProvider prepareFormDataProvider
     */
    public function testPrepareForm($isMetadataEnabled, $result)
    {
        $context = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            '\Magento\Backend\Block\Template\Context'
        );
        $registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            '\Magento\Framework\Registry'
        );
        $formFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            '\Magento\Framework\Data\FormFactory'
        );
        $jsonEncoder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            '\Magento\Framework\Json\EncoderInterface'
        );
        $sourceYesno = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            '\Magento\Backend\Model\Config\Source\Yesno'
        );
        $menuListmode = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            '\Magento\VersionsCms\Model\Source\Hierarchy\Menu\Listmode'
        );
        $menuListtype = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            '\Magento\VersionsCms\Model\Source\Hierarchy\Menu\Listtype'
        );
        $menuChapter = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            '\Magento\VersionsCms\Model\Source\Hierarchy\Menu\Chapter'
        );
        $hierarchyVisibility = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            '\Magento\VersionsCms\Model\Source\Hierarchy\Visibility'
        );
        $menuLayout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            '\Magento\VersionsCms\Model\Source\Hierarchy\Menu\Layout'
        );
        $hierarchyLock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            '\Magento\VersionsCms\Model\Hierarchy\Lock'
        );

        $cmsHierarchyMock = $this->getMockBuilder('\Magento\VersionsCms\Helper\Hierarchy')
            ->setMethods(array('isMetadataEnabled'))
            ->disableOriginalConstructor()
            ->getMock();
        $cmsHierarchyMock->expects($this->any())
            ->method('isMetadataEnabled')
            ->will($this->returnValue($isMetadataEnabled));
        $block = new \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form(
            $context,
            $registry,
            $formFactory,
            $jsonEncoder,
            $cmsHierarchyMock,
            $sourceYesno,
            $menuListmode,
            $menuListtype,
            $menuChapter,
            $hierarchyVisibility,
            $menuLayout,
            $hierarchyLock
        );
        $prepareFormMethod = new \ReflectionMethod(
            'Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form',
            '_prepareForm'
        );
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);
        $form = $block->getForm();
        $this->assertEquals($result, is_null($form->getElement('top_menu_fieldset')));
    }

    /**
     * Data provider for testPrepareForm
     *
     * @return array
     */
    public function prepareFormDataProvider()
    {
        return array(
            array(1, false),
            array(0, true)
        );
    }
}
