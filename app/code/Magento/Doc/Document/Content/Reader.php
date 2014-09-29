<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document\Content;

/**
 * Class Reader
 * @package Magento\Doc\Document\Content
 */
class Reader
{
    /**
     * Name of an attribute that stands for data type of node values
     */
    const TYPE_ATTRIBUTE = 'xsi:type';

    /**
     * @var array
     */
    protected $idAttributes = [
        '*' => 'id',
        'div' => 'module',
        'img' => 'src'
    ];

    /**
     * File locator
     *
     * @var FileResolver
     */
    protected $fileResolver;

    /**
     * The name of file that stores configuration
     *
     * @var string
     */
    protected $fileName;

    /**
     * Path to corresponding XSD file with validation rules for merged config
     *
     * @var string
     */
    protected $schema;

    /**
     * Path to corresponding XSD file with validation rules for separate config files
     *
     * @var string
     */
    protected $perFileSchema;

    /**
     * Dom Interface Factory
     *
     * @var DomFactory
     */
    protected $domFactory;

    /**
     * Should configuration be validated
     *
     * @var bool
     */
    protected $isValidated;

    /**
     * Constructor
     *
     * @param FileResolver $fileResolver
     * @param SchemaLocator $schemaLocator
     * @param ValidationState $validationState
     * @param DomFactory $domFactory
     * @param array $idAttributes
     */
    public function __construct(
        FileResolver $fileResolver,
        SchemaLocator $schemaLocator,
        ValidationState $validationState,
        DomFactory $domFactory,
        $idAttributes = []
    ) {
        $this->fileResolver = $fileResolver;
        $this->schemaFile = $schemaLocator->getSchema();
        $this->perFileSchema = $schemaLocator->getPerFileSchema() &&
        $this->isValidated ? $schemaLocator->getPerFileSchema() : null;
        $this->isValidated = $validationState->isValidated();
        $this->domFactory = $domFactory;
        $this->idAttributes = array_replace($this->idAttributes, $idAttributes);
    }

    /**
     * Load document template
     *
     * @param string $template
     * @return string
     */
    public function read($template)
    {
        $fragments = $this->fileResolver->get($template);
        if (!count($fragments)) {
            return '';
        }
        $output = $this->mergeDocumentFragments($fragments);
        return $output;
    }

    /**
     * Merge document fragments
     *
     * @param \Magento\Framework\View\File[] $fragments
     * @return string
     * @throws \Magento\Framework\Exception
     */
    protected function mergeDocumentFragments(array $fragments)
    {
        /** @var \Magento\Framework\Config\Dom $merger */
        $html = '';
        $merger = $this->domFactory->create(
            [
                'xml' => '<div id="root"></div>',
                'idAttributes' => $this->idAttributes,
                'typeAttributeName' => self::TYPE_ATTRIBUTE,
                'schemaFile' => $this->perFileSchema
            ]
        );
        foreach ($fragments as $key => $fragment) {
            /** @var \Magento\Framework\View\File $fragment */
            try {
                $content = '<div module="'.$fragment->getModule().'">' . file_get_contents($fragment->getFilename()) . '</div>';
                $merger->merge($content);
            } catch (\Magento\Framework\Config\Dom\ValidationException $e) {
                throw new \Magento\Framework\Exception("Invalid XML in file " . $key . ":\n" . $e->getMessage());
            }
        }
        if ($this->isValidated) {
            $errors = [];
            if ($merger && !$merger->validate($this->schemaFile, $errors)) {
                $message = "Invalid Document \n";
                throw new \Magento\Framework\Exception($message . implode("\n", $errors));
            }
        }

        if ($merger) {
            $dom = $merger->getDom();
            foreach ($dom->firstChild->childNodes as $node) {
                $html .= $dom->saveHTML($node);
            }
        }
        return $html;
    }
}
