<?php
/**
 * Tag API test.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Mage/Tag/Model/Api/_files/tag.php
 */
class Mage_Tag_Model_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test info method.
     */
    public function testInfo()
    {
        /** @var Mage_Tag_Model_Tag $tag */
        $tag = Mage::getModel('Mage_Tag_Model_Tag');
        $tagId = $tag->loadByName('tag_name')->getTagId();
        /** Retrieve tag info. */
        $tagInfo = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductTagInfo',
            array($tagId)
        );
        /** Assert response is not empty. */
        $this->assertNotNull($tagInfo, 'Tag info is not retrieved.');
        /** Assert base fields are present in the response. */
        $expectedFields = array('status', 'name', 'base_popularity', 'products');
        $missingFields = array_diff($expectedFields, array_keys($tagInfo));
        $this->assertEmpty(
            $missingFields,
            sprintf("The following fields must be present in response: %s.", implode(', ', $missingFields))
        );
    }

    /**
     * Test update method.
     */
    public function testUpdate()
    {
        /** @var Mage_Tag_Model_Tag $tag */
        $tagId = Mage::getModel('Mage_Tag_Model_Tag')->loadByName('tag_name')->getTagId();
        $updateData = array('name' => 'new_tag_name', 'status' => Mage_Tag_Model_Tag::STATUS_DISABLED);
        /** Update tag. */
        $tagUpdateResponse = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductTagUpdate',
            array($tagId, (object)$updateData)
        );
        /** Assert tag update result. */
        $this->assertTrue((bool)$tagUpdateResponse, 'Tag update was unsuccessful.');
        /** Assert updated fields. */
        /** @var Mage_Tag_Model_Tag $updatedTag */
        $updatedTag = Mage::getModel('Mage_Tag_Model_Tag')->loadByName($updateData['name']);
        $this->assertNotEmpty($updatedTag->getTagId(), 'Tag name update was unsuccessful.');
        $this->assertEquals($updateData['status'], $updatedTag->getStatus(), 'Tag name update was unsuccessful.');
    }
}
