<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Cms\Setup\Block;

/**
 * Class Converter
 */
class Converter
{
    /**
     * @var \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface
     */
    protected $categoryTreeReadService;

    /**
     * @var \Magento\Catalog\Service\V1\Category\CategoryLoader
     */
    protected $categoryLoader;

    /**
     * @param \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface $categoryReadService
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Service\V1\Category\CategoryLoader $categoryLoader
     */
    public function __construct(
        \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface $categoryReadService,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Service\V1\Category\CategoryLoader $categoryLoader
    ) {
        $this->categoryTreeReadService = $categoryReadService;
        $this->eavConfig = $eavConfig;
        $this->categoryLoader = $categoryLoader;
    }

    /**
     * Convert CSV format row to array
     *
     * @param array $row
     * @return array
     */
    public function convertRow($row)
    {
        $data = [];
        foreach ($row as $field => $value) {
            if ('category_path' == $field) {
                $categoryId = $this->getCategoryId($this->getArrayValue($value));
                $data['category_id'] = $categoryId;
                continue;
            }
            if ('content' == $field) {
                $data['block'][$field] = $this->convertContentUrls($value);
                continue;
            }
            $data['block'][$field] = $value;
        }
         return $data;
    }

    /**
     * Get product category ids from array
     *
     * @param array $categories
     * @return int
     */
    protected function getCategoryId($categories)
    {
        $tree = $this->categoryTreeReadService->tree();
        foreach ($categories as $name) {
            foreach ($tree->getChildren() as $child) {
                if (strcasecmp($child->getName(), $name) == 0) {
                    $tree = $child;
                    break;
                }
            }
        }
        return $tree->getId();
    }

    /**
     * Get formatted array value
     *
     * @param mixed $value
     * @return array
     */
    protected function getArrayValue($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (false !== strpos($value, "/")) {
            $value = array_filter(explode("/", $value));
        }
        return !is_array($value) ? [$value] : $value;
    }

    /**
     * @param $content
     * @return mixed
     */
    protected function convertContentUrls($content)
    {
        $categoriesTree = array_filter($this->getContentCategoriesMatches($content));
        if (!empty($categoriesTree)) {
            $categoriesUrls = $this->getCategoriesUrl($categoriesTree[1]);
            foreach ($categoriesUrls as $categoryPath => $categoryUrl) {
                $content = $this->replaceContentCategoriesPath($content, $categoryPath, $categoryUrl);
            }
        }
        return $content;
    }

    /**
     * @param $content
     * @return mixed
     */
    protected function getContentCategoriesMatches($content)
    {
        $regexp = '/{.(?:category_url=")(.+)(?:"}+)/';
        preg_match_all($regexp, $content, $matches);
        return $matches;
    }

    /**
     * @param $content
     * @param $categoryPath
     * @param $categoryUrl
     * @return mixed
     */
    protected function replaceContentCategoriesPath($content, $categoryPath, $categoryUrl)
    {
        $categoryPath = str_replace('/', '\/', $categoryPath);
        $regexp = '/{.(?:category_url=")(' . $categoryPath . '+)(?:"}+)/';
        return preg_replace($regexp, $categoryUrl, $content);
    }

    /**
     * @param $categoriesTree
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCategoriesUrl($categoriesTree)
    {
        foreach ($categoriesTree as $path) {
            $category = $this->categoryLoader->load($this->getCategoryId($this->getArrayValue($path)));
            $categoryData[$path] = $category->getUrl();
            unset($category);
        }
        return $categoryData;
    }

}
