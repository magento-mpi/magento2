<?php
/**
 * @category   Magento
 * @package    Magento_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\PageCache\Model\Indexer;

/**
 * Class Context
 */
class Context implements \Magento\Object\IdentityInterface
{
    /**
     * @var array
     */
    protected $entities = array();

    /**
     * Register entity Ids
     *
     * @param $cacheTag
     * @param $ids
     * @return $this
     */
    public function registerEntities($cacheTag, $ids)
    {
        $this->entities[$cacheTag] =
            array_merge($this->getRegisteredEntity($cacheTag), $ids);
        return $this;
    }

    /**
     * Returns registered entities
     *
     * @param $cacheTag
     * @return array
     */
    public function getRegisteredEntity($cacheTag)
    {
        if (empty($this->entities[$cacheTag])) {
            return array();
        } else {
            return $this->entities[$cacheTag];
        }
    }

    /**
     * Returns identities
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = array();
        foreach ($this->entities as $cacheTag => $ids) {
            foreach ($ids as $id) {
                $identities[] = $cacheTag . '_' . $id;
            }
        }
        return $identities;
    }
}
