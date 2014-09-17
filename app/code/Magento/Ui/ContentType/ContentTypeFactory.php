<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType;

use Magento\Framework\ObjectManager;

/**
 * Class ContentTypeFactory
 */
class ContentTypeFactory
{
    const DEFAULT_TYPE = 'Magento\Ui\ContentType\Html';

    /**
     * @var array
     */
    protected $types = [
        'html' => 'Magento\Ui\ContentType\Html',
        'json' => 'Magento\Ui\ContentType\Json',
        'xml' => 'Magento\Ui\ContentType\Xml',
    ];

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     * @param array $types
     */
    public function __construct(ObjectManager $objectManager, array $types = [])
    {
        $this->types = array_merge($this->types, $types);
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $type
     * @return ContentTypeInterface
     * @throws \InvalidArgumentException
     */
    public function get($type)
    {
        $contentRenderClass = isset($this->types[$type]) ? $this->types[$type] : self::DEFAULT_TYPE;
        $contentRender = $this->objectManager->get($contentRenderClass);
        if (!$contentRender instanceof ContentTypeInterface) {
            throw new \InvalidArgumentException(sprintf("Wrong render for '%s' content type", $type));
        }
        return $contentRender;
    }
}
