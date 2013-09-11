<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Templates collection
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource\Email\Template;

class Collection extends  \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Template table name
     *
     * @var string
     */
    protected $_templateTable;

    /**
     * Define resource table
     *
     */
    public function _construct()
    {
        $this->_init('\Magento\Core\Model\Email\Template', '\Magento\Core\Model\Resource\Email\Template');
        $this->_templateTable = $this->getMainTable();
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('template_id', 'template_code');
    }
}
