<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_CategoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppArea adminhtml
     */
    public function testGetAfterElementHtml()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $layout = Mage::getModel(
            'Magento\Core\Model\Layout',
            array('area' => \Magento\Core\Model\App\Area::AREA_ADMINHTML)
        );

        $block = $objectManager->create('Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Category',
            array('layout' => $layout));

        /** @var $formFactory \Magento\Data\Form\Factory */
        $formFactory = $objectManager->get('Magento\Data\Form\Factory');
        $form = $formFactory->create();
        $block->setForm($form);

        $this->assertRegExp('/<button[^>]*New\sCategory[^>]*>/', $block->getAfterElementHtml());
    }
}
