<?php
/**
 * Test for \Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Main block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Block\Adminhtml\Role\Edit\Tab;

/**
 * @magentoAppArea adminhtml
 */
class MainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @var \Magento\Core\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var \Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Main
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_layout = $this->_objectManager->get('Magento\Core\Model\Layout');
        $this->_blockFactory = $this->_objectManager->get('Magento\Core\Model\BlockFactory');
        $this->_block = $this->_blockFactory->createBlock('Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Main');
        $this->_layout->addBlock($this->_block);
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Magento\Core\Model\Layout');
        $this->_objectManager->removeSharedInstance('Magento\Core\Model\BlockFactory');
        unset($this->_objectManager, $this->_layout, $this->_blockFactory, $this->_block);
    }

    /**
     * Test _prepareForm method.
     *
     * @dataProvider prepareFormDataProvider
     * @param \Magento\Object $apiRole
     * @param array $formElements
     */
    public function testPrepareForm($apiRole, array $formElements)
    {
        // TODO: Move to unit tests after MAGETWO-4015 complete
        $this->assertEmpty($this->_block->getForm());

        $this->_block->setApiRole($apiRole);
        $this->_block->toHtml();

        $form = $this->_block->getForm();
        $this->assertInstanceOf('Magento\Data\Form', $form);
        /** @var \Magento\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->getElement('base_fieldset');
        $this->assertInstanceOf('Magento\Data\Form\Element\Fieldset', $fieldset);
        $elements = $fieldset->getElements();
        foreach ($formElements as $elementId) {
            $element = $elements->searchById($elementId);
            $this->assertNotEmpty($element, "Element '$elementId' is not found in form fieldset");
            $this->assertEquals($apiRole->getData($elementId), $element->getValue());
        }
    }

    /**
     * @return array
     */
    public function prepareFormDataProvider()
    {
        return array(
            'Empty API Role' => array(
                new \Magento\Object(),
                array(
                    'role_name',
                    'in_role_user',
                    'in_role_user_old'
                )
            ),
            'New API Role' => array(
                new \Magento\Object(array(
                    'role_name' => 'Role'
                )),
                array(
                    'role_name',
                    'in_role_user',
                    'in_role_user_old'
                )
            ),
            'Existed API Role' => array(
                new \Magento\Object(array(
                    'id' => 1,
                    'role_name' => 'Role'
                )),
                array(
                    'role_id',
                    'role_name',
                    'in_role_user',
                    'in_role_user_old'
                )
            )
        );
    }
}
