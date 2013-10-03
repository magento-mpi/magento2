<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sitemap resource model
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sitemap\Model\Resource;

class Sitemap extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('sitemap', 'sitemap_id');
    }
}
