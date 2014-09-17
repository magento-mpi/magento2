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
    /**
     * Default content type
     */
    const DEFAULT_TYPE = 'Magento\Ui\ContentType\Html';

    /**
     * Content types
     *
     * @var array
     */
    protected $types = [
        'html' => 'Magento\Ui\ContentType\Html',
        'json' => 'Magento\Ui\ContentType\Json',
        'xml' => 'Magento\Ui\ContentType\Xml',
    ];

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     * @param array $types
     */
    public function __construct(ObjectManager $objectManager, array $types = [])
    {
        $this->types = array_merge($this->types, $types);
        $this->objectManager = $objectManager;
    }

    /**
     * Get content type object instance
     *
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
