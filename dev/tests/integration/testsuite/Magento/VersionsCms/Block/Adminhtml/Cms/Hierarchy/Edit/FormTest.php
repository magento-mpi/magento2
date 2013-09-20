<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Model\Layout */
    protected $_layout = null;

    /** @var \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        $this->_block = $this->_layout->createBlock('Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form');
    }

    public function testGetGridJsObject()
    {
        $parentName = 'parent';
        $mockClass = $this->getMockClass('Magento\Catalog\Block\Product\AbstractProduct', array('_prepareLayout'),
            array(\Mage::getModel('Magento\Core\Block\Template\Context'))
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
}
