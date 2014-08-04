<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Plugin;

/**
 * Plugin for product attribute tabs
 */
class Tabs
{
    /** @var \Magento\Framework\Module\Manager  */
    protected $_moduleManager;

    /**
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(\Magento\Framework\Module\Manager $moduleManager)
    {
        $this->_moduleManager = $moduleManager;
    }

    /**
     * @param \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs $subject
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection $result
     *
     * @return \Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetGroupCollection(\Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs $subject, $result)
    {
        if (!$this->_moduleManager->isOutputEnabled('Magento_RecurringPayment')) {
            foreach ($result as $key => $group) {
                if ($group->getAttributeGroupCode() === 'recurring-payment') {
                    $result->removeItemByKey($key);
                }
            }
        }
        return $result;
    }
}
