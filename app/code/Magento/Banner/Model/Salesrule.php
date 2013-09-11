<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */


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
namespace Magento\Banner\Model;

class Salesrule extends \Magento\Core\Model\AbstractModel
{
    /**
     * Initialize promo shopping cart price rule model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Banner\Model\Resource\Salesrule');
    }
}
