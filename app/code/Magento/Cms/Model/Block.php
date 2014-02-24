<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model;

/**
 * CMS block model
 *
 * @method \Magento\Cms\Model\Resource\Block _getResource()
 * @method \Magento\Cms\Model\Resource\Block getResource()
 * @method string getTitle()
 * @method \Magento\Cms\Model\Block setTitle(string $value)
 * @method string getIdentifier()
 * @method \Magento\Cms\Model\Block setIdentifier(string $value)
 * @method string getContent()
 * @method \Magento\Cms\Model\Block setContent(string $value)
 * @method string getCreationTime()
 * @method \Magento\Cms\Model\Block setCreationTime(string $value)
 * @method string getUpdateTime()
 * @method \Magento\Cms\Model\Block setUpdateTime(string $value)
 * @method int getIsActive()
 * @method \Magento\Cms\Model\Block setIsActive(int $value)
 */
class Block extends \Magento\Core\Model\AbstractModel
{
    /**
     * CMS block cache tag
     */
    const CACHE_TAG = 'cms_block';

    /**
     * @var string
     */
    protected $_cacheTag = 'cms_block';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'cms_block';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Cms\Model\Resource\Block');
    }

    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Core\Model\AbstractModel
     * @throws \Magento\Core\Exception
     */
    protected function _beforeSave()
    {
        $needle = 'block_id="' . $this->getBlockId() . '"';
        if (false == strstr($this->getContent(), $needle)) {
            return parent::_beforeSave();
        }
        throw new \Magento\Core\Exception(
            __('Make sure that static block content does not reference the block itself.')
        );
    }
}
