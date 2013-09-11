<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_WeightTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $type
     * @dataProvider virtualTypesDataProvider
     */
    public function testIsVirtualChecked($type)
    {
        $currentProduct = Mage::getModel('Magento\Catalog\Model\Product');
        $currentProduct->setTypeInstance(Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create($type));

        $block = new \Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Weight();

        $form = new \Magento\Data\Form();
        $form->setDataObject($currentProduct);
        $block->setForm($form);

        $this->assertContains('checked="checked"', $block->getElementHtml(),
            'Is Virtual checkbox is not selected for virtual products');
    }

    /**
     * @return array
     */
    public static function virtualTypesDataProvider()
    {
        return array(
            array('Magento\Catalog\Model\Product\Type\Virtual'),
            array('Magento\Downloadable\Model\Product\Type'),
        );
    }

    /**
     * @param string $type
     * @dataProvider physicalTypesDataProvider
     */
    public function testIsVirtualUnchecked($type)
    {
        $currentProduct = Mage::getModel('Magento\Catalog\Model\Product');
        $currentProduct->setTypeInstance(Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create($type));

        $block = new \Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Weight();

        $form = new \Magento\Data\Form();
        $form->setDataObject($currentProduct);
        $block->setForm($form);

        $this->assertNotContains('checked="checked"', $block->getElementHtml(),
            'Is Virtual checkbox is selected for physical products');
    }

    /**
     * @return array
     */
    public static function physicalTypesDataProvider()
    {
        return array(
            array('Magento\Catalog\Model\Product\Type\Simple'),
            array('Magento\Bundle\Model\Product\Type'),
        );
    }
}
