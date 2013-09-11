<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Magento/SalesRule/_files/cart_rule_40_percent_off.php
 * @magentoDataFixture Magento/SalesRule/_files/cart_rule_50_percent_off.php
 * @magentoAppArea adminhtml
 */
class Magento_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_SalesruleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Promotions\Salesrule
     */
    private $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock(
            '\Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Promotions\Salesrule'
        );
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testGetCollection()
    {
        /** @var \Magento\SalesRule\Model\Rule $ruleOne */
        $ruleOne = Mage::getModel('Magento\SalesRule\Model\Rule');
        $ruleOne->load('40% Off on Large Orders', 'name');

        /** @var \Magento\SalesRule\Model\Rule $ruleTwo */
        $ruleTwo = Mage::getModel('Magento\SalesRule\Model\Rule');
        $ruleTwo->load('50% Off on Large Orders', 'name');

        $this->assertEquals(array($ruleOne->getId(), $ruleTwo->getId()), $this->_block->getCollection()->getAllIds());
    }
}
