<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Attribute Set creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CmsStaticBlocks_CreateTest extends Mage_Selenium_TestCase
{

    protected static $blockToBeDeleted = null;

    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to CMS -> Static Blocks</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_cms_static_blocks');
        $this->assertTrue($this->checkCurrentPage('manage_cms_static_blocks'), $this->messages);
        $this->addParameter('id', '0');
    }

    /**
     * <p>Creating a new static block</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Block"</p>
     * <p>2. Fill in the fields</p>
     * <p>3. Click button "Save Block"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the block has been saved.</p>
     *
     * @test
     */
    public function createNew()
    {
        //Data
        $setData = $this->loadData('static_block', null, array('block_title', 'block_identifier'));
        $this->addParameter('blockName', $setData['block_title']);
        //Steps
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        //Verifying
        $this->assertTrue($this->checkCurrentPage('manage_cms_static_blocks'), $this->messages);
        $this->assertTrue($this->successMessage('success_saved_block'), $this->messages);
        //Cleanup
        self::$blockToBeDeleted = $setData;
    }

    /**
     * <p>Creating a new static block with special values (long, special chars).</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Block"</p>
     * <p>2. Fill in the fields</p>
     * <p>3. Click button "Save Block"</p>
     * <p>4. Open the block</p>
     * <p>Expected result:</p>
     * <p>All fields has the same values.</p>
     *
     * @dataProvider dataSpecialValues
     * @depends createNew
     * @test
     *
     * @param array $specialValue
     * @param string|array $randomValue
     */
    public function withSpecialValues(array $specialValue, $randomValue = null)
    {
        //Data
        $setData = $this->loadData('static_block', $specialValue, $randomValue);
        $this->addParameter('blockName', $setData['block_title']);
        //Steps
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        //Verifying
        $this->assertTrue($this->checkCurrentPage('manage_cms_static_blocks'), $this->messages);
        $this->assertTrue($this->successMessage('success_saved_block'), $this->messages);
        $this->cmsStaticBlocksHelper()->openStaticBlock($setData);
        $this->verifyForm($setData, null, array('text_editor'));
        //Cleanup
        self::$blockToBeDeleted = $setData;
    }

    public function dataSpecialValues()
    {
        return array(
            array(array('block_title' => $this->generate('string', 255)), 'block_identifier'),
            array(array('block_identifier' => $this->generate('string', 255, ':alpha:'))),
            array(array('block_title' => $this->generate('string', 50, ':punct:')), 'block_identifier'),
        );
    }

    /**
     * <p>Creating a new static block with empty required fields.</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Block;"</p>
     * <p>2. Fill in the fields, but leave one required field empty;</p>
     * <p>3. Click button "Save Block".</p>
     * <p>Expected result:</p>
     * <p>Received error message "This is a required field."</p>
     *
     * @dataProvider dataEmptyRequiredFields
     * @test
     *
     * @param string $emptyField Name of the field to leave empty
     * @param string $validationMessage Validation message to be verified
     */
    public function withEmptyRequiredFields($emptyField, $validationMessage)
    {
        //Data
        $setData = $this->loadData('static_block', array($emptyField => ''));
        //Steps
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        //Verifying
        $this->assertTrue($this->validationMessage($validationMessage), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function dataEmptyRequiredFields()
    {
        return array(
            array('block_title', 'specify_title'),
            array('block_identifier', 'specify_identifier'),
            array('simple_editor_content', 'specify_content')
        );
    }

    /**
     * <p>Creating a new static block with invalid XML identifier.</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Block"</p>
     * <p>2. Fill in the fields, enter invalid XML identifier</p>
     * <p>3. Click button "Save Block"</p>
     * <p>Expected result:</p>
     * <p>Received an error message about invalid XML identifier.</p>
     *
     * @dataProvider dataInvalidXmlIdentifier
     * @test
     */
    public function withInvalidXmlIdentifier($invalidValue)
    {
        //Data
        $setData = $this->loadData('static_block', array('block_identifier' => $invalidValue));
        //Steps
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        //Verifying
        $this->assertTrue($this->validationMessage('specify_valid_xml_identifier'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function dataInvalidXmlIdentifier()
    {
        return array(
            array($this->generate('string', 12, ':digit:')),
            array($this->generate('string', 12, ':punct:')),
            array("with_a_space " . $this->generate('string', 12, ':alpha:'))
        );
    }

    /**
     * <p>Creating a new static block with existing XML identifier.</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Block"</p>
     * <p>2. Fill in the fields, enter already existing identifier</p>
     * <p>3. Click button "Save Block"</p>
     * <p>Expected result:</p>
     * <p>Received an error message about already existing identifier.</p>
     *
     * @depends createNew
     * @test
     */
    public function withExistingIdentifier()
    {
        $this->markTestIncomplete(
                'This test is skipped because behaves differently with different Magento configuration. '
                . 'Depends on the number of store views.'
        );
    }

    protected function tearDown()
    {
        if (isset(self::$blockToBeDeleted)) {
            $this->cmsStaticBlocksHelper()->openStaticBlock(self::$blockToBeDeleted);
            $this->cmsStaticBlocksHelper()->deleteStaticBlock();
            self::$blockToBeDeleted = null;
        }
    }

}
