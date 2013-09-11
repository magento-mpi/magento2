<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_CategoryTest extends PHPUnit_Framework_TestCase
{
    public function testGetAfterElementHtml()
    {
        $layout = Mage::getModel(
            '\Magento\Core\Model\Layout',
            array('area' => \Magento\Core\Model\App\Area::AREA_ADMINHTML)
        );

        $block = new \Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Category(array(), $layout);

        $form = new \Magento\Data\Form();
        $block->setForm($form);

        $this->assertRegExp('/<button[^>]*New\sCategory[^>]*>/', $block->getAfterElementHtml());
    }
}
