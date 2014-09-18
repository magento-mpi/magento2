<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Setup\Module\Setup;

class OrderIncrementPrefix
{
    /**#@+
     * Data key
     */
    const KEY_ORDER_INCREMENT_PREFIX = 'order_increment_prefix';
    /**#@- */

    /**
     * Setup
     *
     * @var Setup
     */
    private $setup;

    /**
     * Configurations
     *
     * @var []
     */
    private $data;

    /**
     * Default Constructor
     *
     * @param Setup $setup
     * @param array $data
     */
    public function __construct(Setup $setup, array $data)
    {
        $this->setup  = $setup;
        $this->data = $data;
    }
    /**
     * Saves order increment prefix to DB
     *
     * @return void
     */
    public function save()
    {
        if (isset($this->data[self::KEY_ORDER_INCREMENT_PREFIX])) {
            $sql = 'SELECT entity_type_id FROM eav_entity_type where entity_type_code=\'order\'';
            $data = array(
                'entity_type_id' => $this->setup->getConnection()->fetchOne($sql, 'entity_type_id'),
                'store_id' => '1',
                'increment_prefix' => $this->data[self::KEY_ORDER_INCREMENT_PREFIX]
            );
            $this->setup->getConnection()->insert($this->setup->getTable('eav_entity_store'), $data);
        }
    }
}
