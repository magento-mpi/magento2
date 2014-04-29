<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Adminhtml\System\Config\Source;

/**
 * Catalog search types
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Engine implements \Magento\Framework\Option\ArrayInterface
{
    const FULLTEXT = 'Magento\CatalogSearch\Model\Resource\Fulltext\Engine';

    const SOLR = 'Magento\Search\Model\Resource\Engine';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $engines = array(self::FULLTEXT => __('MySql Fulltext'), self::SOLR => __('Solr'));
        $options = array();
        foreach ($engines as $k => $v) {
            $options[] = array('value' => $k, 'label' => $v);
        }
        return $options;
    }
}
