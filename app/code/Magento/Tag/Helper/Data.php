<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tag data helper
 */
class Magento_Tag_Helper_Data extends Magento_Core_Helper_Abstract
{
    public function getStatusesArray()
    {
        return array(
            Magento_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('Magento_Tag_Helper_Data')->__('Disabled'),
            Magento_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('Magento_Tag_Helper_Data')->__('Pending'),
            Magento_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('Magento_Tag_Helper_Data')->__('Approved')
        );
    }

    public function getStatusesOptionsArray()
    {
        return array(
            array(
                'label' => Mage::helper('Magento_Tag_Helper_Data')->__('Disabled'),
                'value' => Magento_Tag_Model_Tag::STATUS_DISABLED
            ),
            array(
                'label' => Mage::helper('Magento_Tag_Helper_Data')->__('Pending'),
                'value' => Magento_Tag_Model_Tag::STATUS_PENDING
            ),
            array(
                'label' => Mage::helper('Magento_Tag_Helper_Data')->__('Approved'),
                'value' => Magento_Tag_Model_Tag::STATUS_APPROVED
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
