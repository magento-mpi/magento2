<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Layout\Reference;
use Magento\Tools\Layout\Formatter;
/**
 * Processor
 */
class Processor
{
    /**
     * @var Formatter
     */
    protected $_formatter;

    /**
     * @var \SimpleXMLElement
     */
    protected $_referenceList;

    /**
     * @var string
     */
    protected $_referencesFile;

    /**
     * @var array
     */
    protected $_referencePattern = array(
        'reference' => '//reference[@name]',
        'block' => '//block[@name]',
        'container' => '//container[@name]',
    );

    /**
     * @param Formatter $formatter
     * @param string $referencesFile
     */
    function __construct(Formatter $formatter, $referencesFile)
    {
        $this->_formatter = $formatter;
        $this->_referencesFile = $referencesFile;
    }


    /**
     * Create list from array
     *
     * @param array $data
     * @param string $type
     * @return Processor
     */
    protected function _addElements($data, $type)
    {
        array_walk_recursive($data, function($value) use ($type) {
            $element = $this->_referenceList->addChild('item');
            $element->addAttribute('type', $type);
            $element->addAttribute('value', $value);
        });

        return $this;
    }

    /**
     * Get layout file from Magento root directory
     *
     * @param $path
     * @return array
     */
    public function getLayoutFiles($path)
    {
        $result = array();
        $patterns = array(
            '/app/design/*/*/*/layout/*.xml',
            '/app/design/*/*/*/layout/*/*.xml',
            '/app/design/*/*/*/layout/*/*/*/*.xml',
            '/app/design/*/*/*/layout/*/*/*/*/*.xml',
            '/app/design/*/*/*/layout/*/*/*/*/*/*.xml',
            '/app/code/*/*/*/*/layout/*.xml',
            '/app/code/*/*/*/*/layout/*/*.xml',
            '/app/code/*/*/*/*/layout/*/*/*/*.xml',
            '/app/code/*/*/*/*/layout/*/*/*/*/*.xml',
            '/app/code/*/*/*/*/layout/*/*/*/*/*/*.xml',
        );

        foreach ($patterns as $pattern) {
            $result = array_merge($result, glob($path . $pattern));
        }
        return $result;
    }

    /**
     * Retrieve references and referenced names from $layouts files and
     *
     * @param array $layouts
     * @return Processor
     * @throws \Exception
     */
    public function getReferences($layouts)
    {
        if (empty($layouts)) {
            throw new \Exception("No layouts found");
        }
        $this->_referenceList = new \SimpleXMLElement('<list/>');
        $references = array();
        foreach ($this->_referencePattern as $patternName => $xpath) {
            $result = array();
            foreach ($layouts as $layout) {
                $xml = simplexml_load_file($layout);
                $nodes = $xml->xpath($xpath);
                foreach ($nodes as $node) {
                    $result[(string)$node['name']] = '';
                }
            }
            $resultPrint = array_keys($result);
            sort($resultPrint);
            $references[$patternName] = $resultPrint;
        }

        $conflictReferences = $references['reference'];
        foreach ($references as $key => $names) {
            $this->_addElements($names, $key);
            if ($key!='reference') {
                $conflictReferences = array_diff($conflictReferences, $names);
            }
        }
        $this->_addElements($conflictReferences, 'conflictReferences');
        $this->_addElements(array_intersect($references['block'], $references['container']), 'conflictNames');
        return $this;
    }

    /**
     * Update layout files to new format of references using $processor
     *
     * @param array $layouts
     * @param string $processor
     * @param bool $overwrite
     */
    public function updateReferences($layouts, $processor = '', $overwrite = true)
    {
        if (empty($processor)) {
            $processor = __DIR__ . '/../processors/layoutReferences.xsl';
        }
        if (!file_exists($processor)) {
            return;
        }
        $stylesheet = new \DOMDocument();
        $stylesheet->preserveWhiteSpace = true;
        $stylesheet->load($processor);

        $xslt = new \XSLTProcessor();
        $xslt->registerPHPFunctions();
        $xslt->importStylesheet($stylesheet);
        $xslt->setParameter('', 'referencesFile', str_replace('\\', '/', $this->_referencesFile));

        foreach ($layouts as $layout) {
            $doc = new \DOMDocument();
            $doc->preserveWhiteSpace = true;
            $doc->load($layout);

            $transformedDoc = $xslt->transformToXml($doc);
            $result = $this->_formatter->format($transformedDoc);
            if ($overwrite) {
                file_put_contents($layout, $result);
            } else {
                echo $result;
            }
        }
    }

    /**
     * Write reference list to the file
     *
     * @return Processor
     */
    public function writeToFile()
    {
        $result = $this->_formatter->format($this->_referenceList->asXML());
        file_put_contents($this->_referencesFile, $result);
        return $this;
    }
}
