<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Generator;

use Magento\Tools\I18n\Code\Dictionary;

/**
 * Generator factory
 */
class Factory
{
    /**
     * Create Generator
     *
     * @param array $options
     * @return Dictionary\Generator
     */
    public function create(array $options)
    {
        $filesCollector = new Dictionary\FilesCollector();
        $contextDetector = new Dictionary\ContextDetector();

        $parser = new Dictionary\Parser\Composite();

        if (isset($options['php'])) {
            $parser->add(
                new Dictionary\Parser\Php($filesCollector->getFiles(
                    $options['php']['paths'],
                    $options['php']['fileMask']
                ),
                $contextDetector,
                new \Magento_Tokenizer_PhraseCollector()
            ));
        }

        if (isset($options['xml'])) {
            $parser->add(new Dictionary\Parser\Xml(
                $filesCollector->getFiles(
                    $options['js']['paths'],
                    $options['xml']['fileMask']
                ),
                $contextDetector
            ));
        }

        if (isset($options['js'])) {
            $parser->add(new Dictionary\Parser\Js(
                $filesCollector->getFiles(
                    $options['js']['paths'],
                    $options['js']['fileMask']
                ),
                $contextDetector
            ));
        }

        $writer = isset($options['outputFilename']) ? new Dictionary\Writer\Csv($options['outputFilename'])
            : new Dictionary\Writer\Csv\Stdo();

        return new Dictionary\Generator($parser, $writer);
    }
}
