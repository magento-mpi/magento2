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

namespace Magento\Adminhtml\Block\Catalog\Product\Helper\Form;

class WeightTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $type
     * @dataProvider virtualTypesDataProvider
     */
    public function testIsVirtualChecked($type)
    {
        $currentProduct = \Mage::getModel('Magento\Catalog\Model\Product');
        $currentProduct->setTypeInstance(\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($type));

        $block = Mage::getObjectManager()->create('Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Weight');

        $form = \Mage::getObjectManager()->create('Magento\Data\Form');
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
        $currentProduct = \Mage::getModel('Magento\Catalog\Model\Product');
        $currentProduct->setTypeInstance(\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($type));

        $block = Mage::getObjectManager()->create('Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Weight');

        $form = \Mage::getObjectManager()->create('Magento\Data\Form');
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
