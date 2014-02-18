<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sitemap\Model\Resource;

/**
 * Sitemap resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Sitemap extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sitemap', 'sitemap_id');
    }
}
