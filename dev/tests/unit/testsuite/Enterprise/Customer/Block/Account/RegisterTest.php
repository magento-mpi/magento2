<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Enterprise_Customer_Block_Account_RegisterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
    }

    public function testToHtml()
    {
        /** @var  Mage_Core_Block_Template_Context $context */
        $context = $this->_objectManagerHelper->getObject('Mage_Core_Block_Template_Context');

        /** @var Enterprise_Invitation_Block_Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Enterprise_Invitation_Block_Link',
            array(
                'context' => $context,
            )
        );
        $context->getStoreConfig()
            ->expects($this->any())
            ->method('getConfigFlag')
            ->with(
                'enterprise_invitation/general/registration_required_invitation'
            )->will(
                $this->returnValue(true)
            );
        $this->assertEquals('', $block->toHtml());
    }
}
 