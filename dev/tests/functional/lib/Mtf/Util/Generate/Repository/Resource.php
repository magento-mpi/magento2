<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate\Repository;

/**
 * Class Resource
 *
 * @package Mtf\Util\Generate\Repository
 */
class Resource extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Set fixture entity_type
     *
     * @param array $fixture
     */
    public function setFixture(array $fixture)
    {
        $this->_mainTable = $fixture['entity_type'];
    }

    /**
     * Load an object
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @param mixed $value
     * @param null $field
     * @return \Magento\Core\Model\Resource\Db\AbstractDb|void
     */
    public function load(\Magento\Core\Model\AbstractModel $object, $value, $field = null)
    {
        // forbid using resource model
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        //
    }
}
