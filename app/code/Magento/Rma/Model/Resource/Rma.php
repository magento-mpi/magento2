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
class Rma extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_rma', 'entity_id');
    }
}
