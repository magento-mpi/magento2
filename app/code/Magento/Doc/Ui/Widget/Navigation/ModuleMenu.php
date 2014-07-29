<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Ui\Widget\Navigation;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Block\IdentityInterface;

class ModuleMenu extends Template implements IdentityInterface
{
    /**
     * @var \Magento\Framework\Module\ModuleList
     */
    protected $moduleList;

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\Module\ModuleList $moduleList
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Module\ModuleList $moduleList,
        array $data = [])
    {
        $this->moduleList = $moduleList;
        parent::__construct($context, $data);
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Doc\Document::CACHE_TAG];
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
     * @return string
     */
    public function renderMenuHtml()
    {
        $modules = $this->moduleList->getModules();
        $output = '<ul class="modules">';
        foreach ($modules as $module) {
            $output .= '<li><a href="'. $this->getUrl('doc/module/index', ['article' => $module['name']]).'">' . substr($module['name'], 8) . '</a></li>';
        }
        $output .= '</ul>';
        return $output;
    }
}
