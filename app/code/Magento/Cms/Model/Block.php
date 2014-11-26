<?php
/**
 * {license_notice}
 *
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
class Block extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\Object\IdentityInterface
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
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Model\Exception
     */
    public function beforeSave()
    {
        $needle = 'block_id="' . $this->getBlockId() . '"';
        if (false == strstr($this->getContent(), $needle)) {
            return parent::beforeSave();
        }
        throw new \Magento\Framework\Model\Exception(
            __('Make sure that static block content does not reference the block itself.')
        );
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return array(self::CACHE_TAG . '_' . $this->getId());
    }
}
