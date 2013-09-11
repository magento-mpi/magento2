<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Quote resource
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerCustomAttributes\Model\Resource\Sales;

class Quote extends \Magento\CustomerCustomAttributes\Model\Resource\Sales\AbstractSales
{
    /**
     * Main entity resource model name
     *
     * @var string
     */
    protected $_parentResourceModelName = '\Magento\Sales\Model\Resource\Quote';

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('magento_customercustomattributes_sales_flat_quote', 'entity_id');
    }
}
