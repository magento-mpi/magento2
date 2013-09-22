<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog search types
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Model\Adminhtml\System\Config\Source;

class Engine implements \Magento\Core\Model\Option\ArrayInterface
{
    const FULLTEXT = 'Magento\CatalogSearch\Model\Resource\Fulltext\Engine';
    const SOLR = 'Magento\Search\Model\Resource\Engine';

    public function toOptionArray()
    {
        $engines = array(
            self::FULLTEXT => __('MySql Fulltext'),
            self::SOLR => __('Solr')
        );
        $options = array();
        foreach ($engines as $k => $v) {
            $options[] = array(
                'value' => $k,
                'label' => $v
            );
        }
        return $options;
    }
}
