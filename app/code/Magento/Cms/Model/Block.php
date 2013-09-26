<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CMS block model
 *
 * @method Magento_Cms_Model_Resource_Block _getResource()
 * @method Magento_Cms_Model_Resource_Block getResource()
 * @method string getTitle()
 * @method Magento_Cms_Model_Block setTitle(string $value)
 * @method string getIdentifier()
 * @method Magento_Cms_Model_Block setIdentifier(string $value)
 * @method string getContent()
 * @method Magento_Cms_Model_Block setContent(string $value)
 * @method string getCreationTime()
 * @method Magento_Cms_Model_Block setCreationTime(string $value)
 * @method string getUpdateTime()
 * @method Magento_Cms_Model_Block setUpdateTime(string $value)
 * @method int getIsActive()
 * @method Magento_Cms_Model_Block setIsActive(int $value)
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Cms_Model_Block extends Magento_Core_Model_Abstract
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
        $this->_init('Magento_Cms_Model_Resource_Block');
    }

    /**
     * Prevent blocks recursion
     *
     * @return Magento_Core_Model_Abstract
     * @throws Magento_Core_Exception
     */
    protected function _beforeSave()
    {
        $needle = 'block_id="' . $this->getBlockId() . '"';
        if (false == strstr($this->getContent(), $needle)) {
            return parent::_beforeSave();
        }
        throw new Magento_Core_Exception(
            __('Make sure that static block content does not reference the block itself.')
        );
    }
}
