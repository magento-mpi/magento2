<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Banner Salesrule Resource Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Model\Resource;

class Salesrule extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize banner sales rule resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_banner_salesrule', 'rule_id');
    }
}
