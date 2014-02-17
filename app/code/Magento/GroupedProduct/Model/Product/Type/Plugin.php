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
     * @param string $result
     * @return mixed
     */
    public function afterGetOptionArray(\Magento\Catalog\Model\Product\Type $subject, $result)
    {
        if (!$this->moduleManager->isOutputEnabled('Magento_GroupedProduct')) {
            unset($result[\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE]);
        }
        return $result;
    }
} 
