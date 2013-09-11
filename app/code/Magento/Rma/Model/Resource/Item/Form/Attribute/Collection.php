<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rma Item Form Attribute Resource Collection
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Model\Resource\Item\Form\Attribute;

class Collection
    extends \Magento\Eav\Model\Resource\Form\Attribute\Collection
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'Magento_Rma';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'rma_item';

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Eav\Model\Attribute', 'Magento\Rma\Model\Resource\Item\Form\Attribute');
    }

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored.
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    protected function _getEavWebsiteTable()
    {
        return $this->getTable('magento_rma_item_eav_attribute_website');
    }
}
