<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form;

class WeightTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_formFactory = $this->_objectManager->create('Magento\Framework\Data\FormFactory');
    }

    /**
     * @param string $type
     * @dataProvider virtualTypesDataProvider
     */
    public function testIsVirtualChecked($type)
    {
        /** @var $currentProduct \Magento\Catalog\Model\Product */
        $currentProduct = $this->_objectManager->create('Magento\Catalog\Model\Product');
        $currentProduct->setTypeInstance($this->_objectManager->create($type));
        /** @var $block \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight */
        $block = $this->_objectManager->create('Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight');
        $form = $this->_formFactory->create();
        $form->setDataObject($currentProduct);
        $block->setForm($form);

        $this->assertContains(
            'checked="checked"',
            $block->getElementHtml(),
            'Is Virtual checkbox is not selected for virtual products'
        );
    }

    /**
     * @return array
     */
    public static function virtualTypesDataProvider()
    {
        return array(
            array('Magento\Catalog\Model\Product\Type\Virtual'),
            array('Magento\Downloadable\Model\Product\Type')
        );
    }

    /**
     * @param string $type
     * @dataProvider physicalTypesDataProvider
     */
    public function testIsVirtualUnchecked($type)
    {
        /** @var $currentProduct \Magento\Catalog\Model\Product */
        $currentProduct = $this->_objectManager->create('Magento\Catalog\Model\Product');
        $currentProduct->setTypeInstance($this->_objectManager->create($type));

        /** @var $block \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight */
        $block = $this->_objectManager->create('Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight');
        $form = $this->_formFactory->create();
        $form->setDataObject($currentProduct);
        $block->setForm($form);

        $this->assertNotContains(
            'checked="checked"',
            $block->getElementHtml(),
            'Is Virtual checkbox is selected for physical products'
        );
    }

    /**
     * @return array
     */
    public static function physicalTypesDataProvider()
    {
        return array(array('Magento\Catalog\Model\Product\Type\Simple'), array('Magento\Bundle\Model\Product\Type'));
    }
}
