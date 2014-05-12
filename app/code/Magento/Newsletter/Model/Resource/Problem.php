<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Model\Resource;

/**
 * Newsletter problem resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Problem extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('newsletter_problem', 'problem_id');
    }
}
