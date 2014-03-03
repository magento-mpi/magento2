<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Model\Product\Type;

use Magento\Module\Manager;

class Plugin
{
    /**
     * @var \Magento\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param Manager $moduleManager
     */
    public function __construct(Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Remove grouped product from list of visible product types
     *
     * @param \Magento\Catalog\Model\Product\Type $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetOptionArray(\Magento\Catalog\Model\Product\Type $subject, array $result)
    {
        if (!$this->moduleManager->isOutputEnabled('Magento_GroupedProduct')) {
            unset($result[Grouped::TYPE_CODE]);
        }
        return $result;
    }
}
