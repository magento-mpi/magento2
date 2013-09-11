<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Newsletter templates collection
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Model\Resource\Template;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Define resource model and model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Newsletter\Model\Template', 'Magento\Newsletter\Model\Resource\Template');
    }

    /**
     * Load only actual template
     *
     * @return \Magento\Newsletter\Model\Resource\Template\Collection
     */
    public function useOnlyActual()
    {
        $this->addFieldToFilter('template_actual', 1);

        return $this;
    }

    /**
     * Returns options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('template_id', 'template_code');
    }
}
