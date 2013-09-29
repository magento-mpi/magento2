<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Review status resource model
 *
 * @category    Magento
 * @package     Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Review\Model\Resource\Review;

class Status extends \Magento\Core\Model\Resource\Db\AbstractDb
{

    /**
     * Resource status model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('review_status', 'status_id');
    }
}
