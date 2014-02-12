<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Model\Product\Type;

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
    public function afterGetOptionArray($result)
    {
        if (!$this->moduleManager->isOutputEnabled('Magento_ConfigurableProduct')) {
            unset($result[Configurable::TYPE_CODE]);
        }
        return $result;
    }
}
