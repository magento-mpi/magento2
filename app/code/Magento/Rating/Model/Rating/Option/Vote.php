<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating vote model
 *
 * @category   Magento
 * @package    Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Rating\Model\Rating\Option;

class Vote extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('\Magento\Rating\Model\Resource\Rating\Option\Vote');
    }
}
