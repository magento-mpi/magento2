<?php
/**
 * Product tag API test.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Magento/Tag/Model/Api/_files/tag.php
 */
class Magento_Tag_Model_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test info method.
     */
    public function testInfo()
    {
        $tagName = 'tag_name';
        $tagStatus = Magento_Tag_Model_Tag::STATUS_APPROVED;
        /** @var Magento_Tag_Model_Tag $tag */
        $tag = Mage::getModel('Magento_Tag_Model_Tag');
        $tagId = $tag->loadByName($tagName)->getTagId();
        /** Retrieve tag info. */
        $tagInfo = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductTagInfo',
            array($tagId)
        );
        /** Assert response is not empty. */
        $this->assertNotEmpty($tagInfo, 'Tag info is not retrieved.');
        /** Assert base fields are present in the response. */
        $expectedFields = array('status', 'name', 'base_popularity', 'products');
        $missingFields = array_diff($expectedFields, array_keys((array)$tagInfo));
        $this->assertEmpty(
            $missingFields,
            sprintf("The following fields must be present in response: %s.", implode(', ', $missingFields))
        );
        /** Assert retrieved tag data is correct. */
        $this->assertEquals($tagInfo->name, $tagName, 'Tag name is incorrect.');
        $this->assertEquals($tagInfo->status, $tagStatus, 'Tag status is incorrect.');
    }

    /**
     * Test update method.
     */
    public function testUpdate()
    {
        /** @var Magento_Tag_Model_Tag $tag */
        $tagId = Mage::getModel('Magento_Tag_Model_Tag')->loadByName('tag_name')->getTagId();
        $updateData = array('name' => 'new_tag_name', 'status' => Magento_Tag_Model_Tag::STATUS_DISABLED);
        /** Update tag. */
        $tagUpdateResponse = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductTagUpdate',
            array($tagId, (object)$updateData)
        );
        /** Check tag update result. */
        $this->assertTrue((bool)$tagUpdateResponse, 'Tag update was unsuccessful.');
        /** Assert updated fields. */
        /** @var Magento_Tag_Model_Tag $updatedTag */
        $updatedTag = Mage::getModel('Magento_Tag_Model_Tag')->loadByName($updateData['name']);
        $this->assertNotEmpty($updatedTag->getTagId(), 'Tag name update was unsuccessful.');
        $this->assertEquals($updateData['status'], $updatedTag->getStatus(), 'Tag status update was unsuccessful.');
    }
}
