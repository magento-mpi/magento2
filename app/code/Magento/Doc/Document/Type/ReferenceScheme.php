<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Document\Type;

use Magento\Doc\Document\Scheme;
use Magento\Doc\Document\Content;
use Magento\Doc\Document\Item;
use Magento\Doc\Document\Type\Factory;

/**
 * Class ReferenceScheme
 * @package Magento\Doc\Document\Type
 */
class ReferenceScheme extends AbstractType implements ReferenceInterface
{
    /**
     * @var Scheme
     */
    protected $scheme;

    /**
     * @var Content
     */
    protected $content;

    /**
     * @var Factory
     */
    protected $typeFactory;

    /**
     * Constructor
     *
     * @param Scheme $scheme
     * @param Content $content
     * @param Factory $typeFactory
     */
    public function __construct(
        Scheme $scheme,
        Content $content,
        Factory $typeFactory
    ) {
        $this->scheme = $scheme;
        $this->content = $content;
        $this->typeFactory = $typeFactory;
    }

    /**
     * Get item content
     *
     * @param Item $item
     * @return string
     */
    public function getContent(Item $item)
    {
        list ($schemeName, $itemName) = explode('::', $item->getData('reference'));
        $scheme = $this->scheme->get($schemeName . '.xml');
        if (!$scheme) {
            $result = "Broken reference to scheme: '{$schemeName}'";
        } else {
            $refItemArray = $this->findItem($scheme, $itemName);
            if ($refItemArray === null) {
                $result = "Broken reference to scheme item. Scheme: '{$schemeName}', Item: '{$itemName}'";
            } else {
                $refItem = new Item($refItemArray);
                $refItem->setData('scheme', $schemeName);
                $result = $this->typeFactory->get($refItem->getData('type'))->getContent($refItem);
                // update original item info
                $item->setData('scheme', $schemeName);
                $item->setData('module', $refItem->getData('module'));
                $item->setData('readonly', true);
            }
        }
        return $result;
    }

    /**
     * @param array $scheme
     * @param string $itemName
     * @return null|array
     */
    protected function findItem(array $scheme, $itemName)
    {
        $result = null;
        if (isset($scheme['content'])) {
            if (isset($scheme['content'][$itemName])) {
                $result = $scheme['content'][$itemName];
            } else {
                foreach ($scheme['content'] as $item) {
                    $result = $this->findItem($item, $itemName);
                    if ($result !== null) {
                        break;
                    }
                }
            }
        }
        return $result;
    }
}
