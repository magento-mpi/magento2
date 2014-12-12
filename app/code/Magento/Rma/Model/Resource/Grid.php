<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Model\Resource;

/**
 * RMA entity resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Grid extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Primary key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_rma_grid', 'entity_id');
    }
}
