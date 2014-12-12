<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Invitation\Model\Resource\Invitation\History;

/**
 * Invitation status history collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Intialize collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\Invitation\Model\Invitation\History',
            'Magento\Invitation\Model\Resource\Invitation\History'
        );
    }
}
