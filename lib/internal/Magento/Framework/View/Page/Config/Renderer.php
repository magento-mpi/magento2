<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Page\Config;

use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Asset\GroupedCollection;

/**
 * Page config Renderer model
 */
class Renderer
{
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\View\Asset\MinifyService
     */
    private $assetMinifyService;

    /**
     * @var \Magento\Framework\View\Asset\MergeService
     */
    private $assetMergeService;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Framework\View\Asset\MinifyService $assetMinifyService
     * @param \Magento\Framework\View\Asset\MergeService $assetMergeService
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(
        Config $pageConfig,
        \Magento\Framework\View\Asset\MinifyService $assetMinifyService,
        \Magento\Framework\View\Asset\MergeService $assetMergeService,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Logger $logger
    ) {
        $this->pageConfig = $pageConfig;
        $this->assetMinifyService = $assetMinifyService;
        $this->assetMergeService = $assetMergeService;
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
        $this->logger = $logger;
    }

    public function renderHeadContent()
    {
        $result = '';
        $result .= $this->renderTitle();
        $result .= $this->renderAssets();
        return $result;
    }

    protected function renderTitle()
    {
        return '<title>' . $this->pageConfig->getTitle() . '</title>' . "\n";
    }

    protected function renderAssets()
    {
        $result = '';
        /** @var $group \Magento\Framework\View\Asset\PropertyGroup */
        foreach ($this->pageConfig->getAssetCollection()->getGroups() as $group) {
            $groupAssets = $this->assetMinifyService->getAssets($group->getAll());
            $groupAssets = $this->processMerge($groupAssets, $group);
            $groupTemplate = $this->getGroupTemplate(
                $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE),
                $this->getGroupAttributes($group)
            );
            $groupHtml = $this->renderAssetHtml($groupTemplate, $groupAssets);
            $groupHtml = $this->processIeCondition($groupHtml, $group);
            $result .= $groupHtml;
        }
        return $result;
    }

    /**
     * @param array $groupAssets
     * @param \Magento\Framework\View\Asset\PropertyGroup $group
     * @return array
     */
    protected function processMerge($groupAssets, $group)
    {
        if ($group->getProperty(GroupedCollection::PROPERTY_CAN_MERGE) && count($groupAssets) > 1) {
            $groupAssets = $this->assetMergeService->getMergedAssets(
                $groupAssets,
                $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE)
            );
        }
        return $groupAssets;
    }

    /**
     * @param \Magento\Framework\View\Asset\PropertyGroup $group
     * @return string|null
     */
    protected function getGroupAttributes($group)
    {
        $attributes = $group->getProperty('attributes');
        if (!empty($attributes)) {
            if (is_array($attributes)) {
                $attributesString = '';
                foreach ($attributes as $name => $value) {
                    $attributesString .= ' ' . $name . '="' . $this->escaper->escapeHtml($value) . '"';
                }
                $attributes = $attributesString;
            } else {
                $attributes = ' ' . $attributes;
            }
        }
        return $attributes;
    }

    /**
     * @param string $contentType
     * @param string|null $attributes
     * @return string
     */
    protected function getGroupTemplate($contentType, $attributes)
    {
        if ($contentType == 'js') {
            $groupTemplate = '<script' . $attributes . ' type="text/javascript" src="%s"></script>' . "\n";
        } else {
            if ($contentType == 'css') {
                $attributes = ' rel="stylesheet" type="text/css"' . ($attributes ?: ' media="all"');
            }
            $groupTemplate = '<link' . $attributes . ' href="%s" />' . "\n";
        }
        return $groupTemplate;
    }

    /**
     * @param string $groupHtml
     * @param \Magento\Framework\View\Asset\PropertyGroup $group
     * @return string
     */
    protected function processIeCondition($groupHtml, $group)
    {
        $ieCondition = $group->getProperty('ie_condition');
        if (!empty($ieCondition)) {
            $groupHtml = '<!--[if ' . $ieCondition . ']>' . "\n" . $groupHtml . '<![endif]-->' . "\n";
        }
        return $groupHtml;
    }

    /**
     * Render HTML tags referencing corresponding URLs
     *
     * @param string $template
     * @param array $assets
     * @return string
     */
    protected function renderAssetHtml($template, $assets)
    {
        $result = '';
        try {
            /** @var $asset \Magento\Framework\View\Asset\AssetInterface */
            foreach ($assets as $asset) {
                $result .= sprintf($template, $asset->getUrl());
            }
        } catch (\Magento\Framework\Exception $e) {
            $this->logger->logException($e);
            $result .= sprintf($template, $this->urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']));
        }
        return $result;
    }
}
