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
     * @var \Magento\Catalog\Model\Resource\Category\CollectionFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Service\V1\Category\CategoryLoader
     */
    protected $categoryLoader;

    /**
     * @var \Magento\Tools\SampleData\Module\Catalog\Setup\Product\Converter
     */
    protected $productConverter;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory
     */
    protected $attrOptionCollectionFactory;

    /**
     * @var array
     */
    protected $attributeCodeOptionsPair;

    /**
     * @var array
     */
    protected $attributeCodeOptionValueIdsPair;

    /**
     * @param \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Service\V1\Category\CategoryLoader $categoryLoader
     * @param \Magento\Tools\SampleData\Module\Catalog\Setup\Product\Converter $productConverter
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Service\V1\Category\CategoryLoader $categoryLoader,
        \Magento\Tools\SampleData\Module\Catalog\Setup\Product\Converter $productConverter,
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->eavConfig = $eavConfig;
        $this->categoryLoader = $categoryLoader;
        $this->productConverter = $productConverter;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
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
            if ('content' == $field) {
                $data['block'][$field] = $this->replaceMatches($value);
                continue;
            }
            $data['block'][$field] = $value;
        }
        return $data;
    }

    /**
     * @param string $urlKey
     * @return \Magento\Framework\Object
     */
    protected function getCategoryByUrlKey($urlKey)
    {
        $category = $this->categoryFactory->create()
            ->addAttributeToFilter('url_key', $urlKey)
            ->addUrlRewriteToResult()
            ->getFirstItem();
        return $category;
    }

    /**
     * Get formatted array value
     *
     * @param mixed $value
     * @param string $separator
     * @return array
     */
    protected function getArrayValue($value, $separator = "/")
    {
        if (is_array($value)) {
            return $value;
        }
        if (false !== strpos($value, $separator)) {
            $value = array_filter(explode($separator, $value));
        }
        return !is_array($value) ? [$value] : $value;
    }

    /**
     * @param string $content
     * @return mixed
     */
    protected function replaceMatches($content)
    {
        $matches = $this->getMatches($content);
        if (!empty($matches['value'])) {
            $replaces = $this->getReplaces($matches);
            $content = preg_replace($replaces['regexp'], $replaces['value'], $content);
        }
        return $content;
    }

    /**
     * @param string $content
     * @return array
     */
    protected function getMatches($content)
    {
        $regexp = '/{{(category[^ ]*) key="([^"]+)"}}/';
        preg_match_all($regexp, $content, $matchesCategory);
        $regexp = '/{{(attribute) key="([^"]*)"}}/';
        preg_match_all($regexp, $content, $matchesAttribute);
        return array(
            'type' => $matchesCategory[1] + $matchesAttribute[1],
            'value' => $matchesCategory[2] + $matchesAttribute[2]
        );
    }

    /**
     * @param array $matches
     * @return array
     */
    protected function getReplaces($matches)
    {
        $replaceData = array();

        foreach ($matches['value'] as $matchKey => $matchValue) {
            $type = trim($matches['type'][$matchKey]);
            switch ($type) {
                case 'category':
                    $category = $this->getCategoryByUrlKey($matchValue);
                    if (empty($category)) {
                        continue;
                    }
                    $categoryUrl = $category->getRequestPath();
                    $replaceData['regexp'][] = '/{{category key="' . $matchValue .'"}}/';
                    $replaceData['value'][] = '{{store url=""}}' . $categoryUrl;
                    break;
                case 'categoryId':
                    $category = $this->getCategoryByUrlKey($matchValue);
                    if (empty($category)) {
                        continue;
                    }
                    $replaceData['regexp'][] = '/{{categoryId key="' . $matchValue .'"}}/';
                    $replaceData['value'][] = sprintf('%03d', $category->getId());
                    break;
                case 'attribute':
                    if (strpos($matchValue, ':') == false) {
                        break;
                    }
                    list($code, $value) = explode(':', $matchValue);

                    if (!empty($code) && !empty($value)) {
                        $replaceData['regexp'][] = '/{{attribute key="' . $matchValue .'"}}/';
                        $replaceData['value'][] = sprintf('%03d', $this->getAttributeOptionValueId($code, $value));
                    }
                    break;
            }
        }
        return $replaceData;
    }

    /**
     * @param string $urlAttributes
     * @return string
     */
    protected function getUrlFilter($urlAttributes)
    {
        $separatedAttributes = $this->getArrayValue($urlAttributes, ';');
        $urlFilter = null;
        foreach ($separatedAttributes as $attributeNumber => $attributeValue) {
            $attributeData = $this->getArrayValue($attributeValue, '=');
            $attributeOptions = $this->productConverter->getAttributeOptions($attributeData[0]);
            $attributeValue = $attributeOptions->getItemByColumnValue('value', $attributeData[1]);
            if ($attributeNumber == 0) {
                $urlFilter = $attributeData[0] . '=' . $attributeValue->getId();
                continue;
            }
            $urlFilter .= '&' . $attributeData[0] . '=' . $attributeValue->getId();
        }
        return $urlFilter;
    }

    /**
     * Get attribute options by attribute code
     *
     * @param string $attributeCode
     * @return \Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection|null
     */
    protected function getAttributeOptions($attributeCode)
    {
        if (!$this->attributeCodeOptionsPair || !isset($this->attributeCodeOptionsPair[$attributeCode])) {
            $this->loadAttributeOptions($attributeCode);
        }
        return isset($this->attributeCodeOptionsPair[$attributeCode])
            ? $this->attributeCodeOptionsPair[$attributeCode]
            : null;
    }

    /**
     * Loads all attributes with options for attribute
     *
     * @param string $attributeCode
     * @return $this
     */
    protected function loadAttributeOptions($attributeCode)
    {
        /** @var \Magento\Catalog\Model\Resource\Product\Attribute\Collection $collection */
        $collection = $this->attributeCollectionFactory->create();
        $collection->addFieldToSelect(array('attribute_code', 'attribute_id'));
        $collection->addFieldToFilter('attribute_code', $attributeCode);
        $collection->setFrontendInputTypeFilter(array('in' => array('select', 'multiselect')));
        foreach ($collection as $item) {
            $options = $this->attrOptionCollectionFactory->create()
                ->setAttributeFilter($item->getAttributeId())->setPositionOrder('asc', true)->load();
            $this->attributeCodeOptionsPair[$item->getAttributeCode()] = $options;
        }
        return $this;
    }

    /**
     * Find attribute option value pair
     *
     * @param string $attributeCode
     * @param string $value
     * @return mixed
     */
    protected function getAttributeOptionValueId($attributeCode, $value)
    {
        if (!empty($this->attributeCodeOptionValueIdsPair[$attributeCode][$value])) {
            return $this->attributeCodeOptionValueIdsPair[$attributeCode][$value];
        }

        $options = $this->getAttributeOptions($attributeCode);
        $opt = [];
        if ($options) {
            foreach ($options as $option) {
                $opt[$option->getValue()] = $option->getId();
            }
        }
        $this->attributeCodeOptionValueIdsPair[$attributeCode] = $opt;
        return $this->attributeCodeOptionValueIdsPair[$attributeCode][$value];
    }
}
