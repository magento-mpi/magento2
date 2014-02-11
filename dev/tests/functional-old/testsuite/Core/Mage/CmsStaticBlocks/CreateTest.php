<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsStaticBlocks
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Attribute Set creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CmsStaticBlocks_CreateTest extends Mage_Selenium_TestCase
{
    protected $_blockToBeDeleted = array();

    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore('StoreView/generic_store_view', 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to CMS -> Static Blocks</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_cms_static_blocks');
    }

    protected function tearDownAfterTest()
    {
        if ($this->_blockToBeDeleted) {
            $this->loginAdminUser();
            $this->navigate('manage_cms_static_blocks');
            $this->cmsStaticBlocksHelper()->deleteStaticBlock($this->_blockToBeDeleted);
            $this->_blockToBeDeleted = array();
        }
    }

    /**
     * <p>Creating a new static block</p>
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-3129
     */
    public function createNewWithReqField()
    {
        //Data
        $setData = $this->loadDataSet('CmsStaticBlock', 'new_static_block');
        //Steps
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_block');

        return $setData;
    }

    /**
     * <p>Creating a new static block with existing XML identifier.</p>
     *
     * @param array $setData
     *
     * @test
     * @depends createNewWithReqField
     * @TestlinkId TL-MAGE-3131
     */
    public function withExistingIdentifier($setData)
    {
        $this->_blockToBeDeleted = $this->loadDataSet(
            'CmsStaticBlock',
            'search_static_block',
            array('filter_block_identifier' => $setData['block_identifier'])
        );
        //Steps
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        //Verifying
        $this->assertMessagePresent('error', 'already_existing_identifier');
    }

    /**
     * <p>Creating a new static block</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3224
     */
    public function createNewWithAllWidgets()
    {
        //Data
        $productData = $this->productHelper()->createSimpleProduct(true);
        $setData = $this->loadDataSet(
            'CmsStaticBlock',
            'static_block_with_all_widgets',
            array('category_path' => $productData['category']['path'],
                'filter_sku'    => $productData['simple']['product_sku'])
        );
        //Steps
        $this->navigate('manage_cms_static_blocks');
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_block');
        $this->_blockToBeDeleted = $this->loadDataSet(
            'CmsStaticBlock',
            'search_static_block',
            array('filter_block_identifier' => $setData['block_identifier'])
        );
    }

    /**
     * <p>Creating a new static block with special values (long, special chars).</p>
     *
     * @param array $specialValue
     *
     * @test
     * @dataProvider withSpecialValuesDataProvider
     * @depends createNewWithReqField
     * @TestlinkId TL-MAGE-3227
     */
    public function withSpecialValues(array $specialValue)
    {
        //Data
        $setData = $this->loadDataSet('CmsStaticBlock', 'new_static_block', $specialValue);
        $blockToOpen = $this->loadDataSet(
            'CmsStaticBlock',
            'search_static_block',
            array('filter_block_identifier' => $setData['block_identifier'])
        );
        //Steps
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_block');
        $this->_blockToBeDeleted = $this->loadDataSet(
            'CmsStaticBlock',
            'search_static_block',
            array('filter_block_identifier' => $setData['block_identifier'])
        );
        //Steps
        $this->cmsStaticBlocksHelper()->openStaticBlock($blockToOpen);
        //Verifying
        $this->assertTrue($this->verifyForm($setData), $this->getParsedMessages());
    }

    public function withSpecialValuesDataProvider()
    {
        return array(
            array(array('block_title' => $this->generate('string', 255, ':alpha:'))),
            array(array('block_identifier' => $this->generate('string', 255, ':alpha:'))),
            array(array('block_title' => $this->generate('string', 50, ':punct:')))
        );
    }

    /**
     * <p>Creating a new static block with empty required fields.</p>
     *
     * @param string $emptyField
     * @param string $fieldType
     *
     * @test
     * @dataProvider withEmptyRequiredFieldsDataProvider
     * @TestlinkId TL-MAGE-3225
     */
    public function withEmptyRequiredFields($emptyField, $fieldType)
    {
        //Data
        $setData = $this->loadDataSet('CmsStaticBlock', 'new_static_block', array($emptyField => '%noValue%'));
        //Steps
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        //Verifying
        if ($emptyField == 'content') {
            $emptyField = 'simple_editor_disabled';
        }
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withEmptyRequiredFieldsDataProvider()
    {
        return array(
            array('block_title', 'field'),
            array('block_identifier', 'field'),
            array('store_view', 'multiselect'),
            array('content', 'field')
        );
    }

    /**
     * <p>Creating a new static block with invalid XML identifier.</p>
     *
     * @param string $invalidValue
     *
     * @test
     * @dataProvider withInvalidXmlIdentifierDataProvider
     * @TestlinkId TL-MAGE-3226
     */
    public function withInvalidXmlIdentifier($invalidValue)
    {
        //Data
        $setData = $this->loadDataSet('CmsStaticBlock', 'new_static_block', array('block_identifier' => $invalidValue));
        //Steps
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        //Verifying
        $this->addFieldIdToMessage('field', 'block_identifier');
        $this->assertMessagePresent('validation', 'specify_valid_xml_identifier');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withInvalidXmlIdentifierDataProvider()
    {
        return array(
            array($this->generate('string', 12, ':digit:')),
            array($this->generate('string', 12, ':punct:')),
            array("with_a_space " . $this->generate('string', 12, ':alpha:'))
        );
    }
}
