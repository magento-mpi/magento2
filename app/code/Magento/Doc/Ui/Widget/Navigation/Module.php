<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Ui\Widget\Navigation;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Module\ModuleList;

class Module extends Template
{
    /**
     * @var ModuleList
     */
    protected $moduleList;

    /**
     * @param Template\Context $context
     * @param ModuleList $moduleList
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ModuleList $moduleList,
        array $data = [])
    {
        $this->moduleList = $moduleList;
        parent::__construct($context, $data);
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $tags = [
            'MODULE_MENU',
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout()
        ];
        return $tags;
    }

    /**
     * Retrieve active modules array
     *
     * @return array
     */
    public function getModules()
    {
        return $this->moduleList->getModules();
    }
}
