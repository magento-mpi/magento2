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

class Magento_Catalog_Block_Product_View_AdditionalTest extends PHPUnit_Framework_TestCase
{
    public function testGetChildHtmlList()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        /** @var $block Magento_Catalog_Block_Product_View_Additional */
        $block = $layout->createBlock('Magento_Catalog_Block_Product_View_Additional', 'block');

        /** @var $childFirst Magento_Core_Block_Text */
        $childFirst = $layout->addBlock('Magento_Core_Block_Text', 'child1', 'block');
        $htmlFirst = '<b>Any html of child1</b>';
        $childFirst->setText($htmlFirst);

        /** @var $childSecond Magento_Core_Block_Text */
        $childSecond = $layout->addBlock('Magento_Core_Block_Text', 'child2', 'block');
        $htmlSecond = '<b>Any html of child2</b>';
        $childSecond->setText($htmlSecond);

        $list = $block->getChildHtmlList();

        $this->assertInternalType('array', $list);
        $this->assertCount(2, $list);
        $this->assertContains($htmlFirst, $list);
        $this->assertContains($htmlSecond, $list);
    }
}
