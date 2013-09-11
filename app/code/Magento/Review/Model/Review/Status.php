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
 * Review status
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Review\Model\Review;

class Status extends \Magento\Core\Model\AbstractModel
{

    public function __construct()
    {
        $this->_init('\Magento\Review\Model\Resource\Review\Status');
    }
}
