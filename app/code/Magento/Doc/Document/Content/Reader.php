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
     * Class of dom configuration document used for merge
     *
     * @var string
     */
    protected $domDocumentClass;

    /**
     * Should configuration be validated
     *
     * @var bool
     */
    protected $isValidated;

    /**
     * @param FileResolver $fileResolver
     * @param SchemaLocator $schemaLocator
     * @param ValidationState $validationState
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        FileResolver $fileResolver,
        SchemaLocator $schemaLocator,
        ValidationState $validationState,
        $idAttributes = [],
        $domDocumentClass = 'Magento\Doc\Document\Content\Dom'
    ) {
        $this->fileResolver = $fileResolver;
        $this->idAttributes = array_replace($this->idAttributes, $idAttributes);
        $this->schemaFile = $schemaLocator->getSchema();
        $this->isValidated = $validationState->isValidated();
        $this->perFileSchema = $schemaLocator->getPerFileSchema() &&
        $this->isValidated ? $schemaLocator->getPerFileSchema() : null;
        $this->domDocumentClass = $domDocumentClass;
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
        $merger = null;
        $html = '';
        $merger = $this->createConfigMerger($this->domDocumentClass, '<div id="root"></div>');
        foreach ($fragments as $key => $fragment) {
            /** @var \Magento\Framework\View\File $fragment */
            try {
                $content = '<div module="'.$fragment->getModule().'">' . file_get_contents($fragment->getFilename()) . '</div>';
                $merger->merge($content, '//div[@id="root"]');
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

    /**
     * Create and return a template merger instance
     *
     * {@inheritdoc}
     */
    protected function createConfigMerger($mergerClass, $initialContents)
    {
        return new $mergerClass($initialContents, $this->idAttributes, self::TYPE_ATTRIBUTE, $this->perFileSchema);
    }
}
