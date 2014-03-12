<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Indexer
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Indexer\Model;

/**
 * Class Context
 */
class CacheContext implements \Magento\Object\IdentityInterface
{
    /**
     * @var array
     */
    protected $entities = array();

    /**
     * Register entity Ids
     *
     * @param string $cacheTag
     * @param array $ids
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
     * @param string $cacheTag
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
