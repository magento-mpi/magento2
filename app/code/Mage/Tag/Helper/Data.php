<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tag data helper
 */
class Mage_Tag_Helper_Data extends Magento_Core_Helper_Abstract
{
    public function getStatusesArray()
    {
        return array(
            Mage_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('Mage_Tag_Helper_Data')->__('Disabled'),
            Mage_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('Mage_Tag_Helper_Data')->__('Pending'),
            Mage_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('Mage_Tag_Helper_Data')->__('Approved')
        );
    }

    public function getStatusesOptionsArray()
    {
        return array(
            array(
                'label' => Mage::helper('Mage_Tag_Helper_Data')->__('Disabled'),
                'value' => Mage_Tag_Model_Tag::STATUS_DISABLED
            ),
            array(
                'label' => Mage::helper('Mage_Tag_Helper_Data')->__('Pending'),
                'value' => Mage_Tag_Model_Tag::STATUS_PENDING
            ),
            array(
                'label' => Mage::helper('Mage_Tag_Helper_Data')->__('Approved'),
                'value' => Mage_Tag_Model_Tag::STATUS_APPROVED
            )
        );
    }

    /**
     * Check tags on the correctness of symbols and split string to array of tags
     *
     * @param string $tagNamesInString
     * @return array
     */
    public function extractTags($tagNamesInString)
    {
        return explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagNamesInString));
    }

    /**
     * Clear tag from the separating characters
     *
     * @param array $tagNamesArr
     * @return array
     */
    public function cleanTags(array $tagNamesArr)
    {
        foreach ($tagNamesArr as $key => $tagName) {
            $tagNamesArr[$key] = trim($tagNamesArr[$key], '\'');
            $tagNamesArr[$key] = trim($tagNamesArr[$key]);
            if ($tagNamesArr[$key] == '') {
                unset($tagNamesArr[$key]);
            }
        }
        return $tagNamesArr;
    }

}
