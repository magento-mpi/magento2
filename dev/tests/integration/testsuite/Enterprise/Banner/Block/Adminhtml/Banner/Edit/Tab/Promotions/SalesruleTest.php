<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/SalesRule/_files/cart_rule_40_percent_off.php
 * @magentoDataFixture Mage/SalesRule/_files/cart_rule_50_percent_off.php
 */
class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_SalesruleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Salesrule
     */
    private $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock(
            'Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Salesrule'
        );
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testGetCollection()
    {
        /** @var Mage_SalesRule_Model_Rule $ruleOne */
        $ruleOne = Mage::getModel('Mage_SalesRule_Model_Rule');
        $ruleOne->load('40% Off on Large Orders', 'name');

        /** @var Mage_SalesRule_Model_Rule $ruleTwo */
        $ruleTwo = Mage::getModel('Mage_SalesRule_Model_Rule');
        $ruleTwo->load('50% Off on Large Orders', 'name');

        $this->assertEquals(array($ruleOne->getId(), $ruleTwo->getId()), $this->_block->getCollection()->getAllIds());
    }
}
