<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Model\Resource\Review;

/**
 * Review status resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Status extends \Magento\Core\Model\Resource\Db\AbstractDb
{

    /**
     * Resource status model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('review_status', 'status_id');
    }
}
