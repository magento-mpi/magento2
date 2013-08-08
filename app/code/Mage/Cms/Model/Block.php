<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CMS block model
 *
 * @method Mage_Cms_Model_Resource_Block _getResource()
 * @method Mage_Cms_Model_Resource_Block getResource()
 * @method string getTitle()
 * @method Mage_Cms_Model_Block setTitle(string $value)
 * @method string getIdentifier()
 * @method Mage_Cms_Model_Block setIdentifier(string $value)
 * @method string getContent()
 * @method Mage_Cms_Model_Block setContent(string $value)
 * @method string getCreationTime()
 * @method Mage_Cms_Model_Block setCreationTime(string $value)
 * @method string getUpdateTime()
 * @method Mage_Cms_Model_Block setUpdateTime(string $value)
 * @method int getIsActive()
 * @method Mage_Cms_Model_Block setIsActive(int $value)
 *
 * @category    Mage
 * @package     Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Cms_Model_Block extends Magento_Core_Model_Abstract
{
    const CACHE_TAG     = 'cms_block';
    protected $_cacheTag= 'cms_block';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'cms_block';

    protected function _construct()
    {
        $this->_init('Mage_Cms_Model_Resource_Block');
    }

    /**
     * Prevent blocks recursion
     *
     * @throws Magento_Core_Exception
     * @return Magento_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $needle = 'block_id="' . $this->getBlockId() . '"';
        if (false == strstr($this->getContent(), $needle)) {
            return parent::_beforeSave();
        }
        Mage::throwException(
            Mage::helper('Mage_Cms_Helper_Data')->__('Make sure that static block content does not reference the block itself.')
        );
    }
}
