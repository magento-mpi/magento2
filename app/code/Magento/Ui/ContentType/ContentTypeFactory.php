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
    const DEFAULT_TYPE = 'html';

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
    public function get($type = ContentTypeFactory::DEFAULT_TYPE)
    {
        if (!isset($this->types[$type])) {
            throw new \InvalidArgumentException(sprintf("Wrong content type '%s', renderer not exists.", $type));
        }

        $contentRender = $this->objectManager->get($this->types[$type]);
        if (!$contentRender instanceof ContentTypeInterface) {
            throw new \InvalidArgumentException(
                sprintf('"%s" must implement the interface ContentTypeInterface.', $this->types[$type])
            );
        }

        return $contentRender;
    }
}
