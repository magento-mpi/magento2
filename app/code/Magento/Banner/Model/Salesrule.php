<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Model;

/**
 * Enterprise banner sales rule model
 *
 * @method \Magento\Banner\Model\Resource\Salesrule _getResource()
 * @method \Magento\Banner\Model\Resource\Salesrule getResource()
 * @method int getBannerId()
 * @method \Magento\Banner\Model\Salesrule setBannerId(int $value)
 * @method int getRuleId()
 * @method \Magento\Banner\Model\Salesrule setRuleId(int $value)
 *
 * @category    Magento
 * @package     Magento_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Salesrule extends \Magento\Model\AbstractModel
{
    /**
     * Initialize promo shopping cart price rule model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Banner\Model\Resource\Salesrule');
    }
}
