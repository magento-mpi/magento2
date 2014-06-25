<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Magento\I18n;

class TranslationFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvParser;

    /**
     * @var string
     */
    protected $baseLocale = 'en_US';

    /**
     * Context
     *
     * @var \Magento\Tools\I18n\Code\Context
     */
    protected $context;

    protected function setUp()
    {
        $this->csvParser = new \Magento\Framework\File\Csv();
        $this->csvParser->setDelimiter(',');
    }

    /**
     * Checked whether all the phrases from en_US.csv file is present in all other locale csv files,
     * and whether there is obsolete
     *
     * @param string $modulePath
     * @dataProvider getModulesPath
     */
    public function testCoincidenceNonEnglishFiles($modulePath)
    {
        $files = $this->getCsvFiles($modulePath);

        $failures = array();
        if (!empty($files)) {
            $failures = $this->checkModuleFiles($files);
        }

        $this->assertEmpty(
            $failures,
            $this->printMessage($failures)
        );
    }

    /**
     * @param string $modulePath
     * @return string[] Array csv files array[$locale]$pathToCsvFile]
     */
    protected function getCsvFiles($modulePath)
    {
        $files = [];
        foreach (glob("{$modulePath}/i18n/*.csv") as $file) {
            $locale = str_replace('.csv', '', basename($file));
            $files[$locale] = $file;
        }
        return $files;
    }

    /**
     * @param string[][][] $failures Array errors in format $failures[$locale][$errorType][$message]
     * @return string
     */
    protected function printMessage($failures)
    {
        $message = "\n";
        foreach ($failures as $locale => $localeErrors) {
            $message .= $locale . "\n";
            foreach ($localeErrors as $typeError => $error) {
                $message .= "\t" . $typeError . "\n";
                foreach ($error as $phrase) {
                    $message .= "\t\t" . $phrase . "\n";
                }
            }
        }
        return $message;
    }

    /**
     * DataProvider
     *
     * @return array
     */
    public function getModulesPath()
    {
        $pathToSource = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        $modules = array();
        foreach (glob("{$pathToSource}/app/code/*/*", GLOB_ONLYDIR) as $modulePath) {
            $modules[basename($modulePath)] = ['modulePath' => $modulePath];
        }
        return $modules;
    }

    /**
     * @param string[][] $files Array csv files in format $files[$locale][$filesPath]
     * @return array
     */
    protected function checkModuleFiles($files)
    {
        $failures = [];
        if (!isset($files[$this->baseLocale])) {
            $failures[$this->baseLocale]['missing'] = ["{$this->baseLocale}.csv file is not found"];
            return $failures;
        }
        $baseLocaleData = $this->csvParser->getDataPairs($files[$this->baseLocale]);
        foreach (array_keys($files) as $locale) {
            $localeFailures = $this->comparePhrase($baseLocaleData, $this->csvParser->getDataPairs($files[$locale]));
            if (!empty($localeFailures)) {
                $failures[$locale] = $localeFailures;
            }
        }
        return $failures;
    }

    /**
     * @param array $baseLocaleData
     * @param array $localeData
     * @return array
     */
    protected function comparePhrase($baseLocaleData, $localeData)
    {
        $missing = array_diff_key($baseLocaleData, $localeData);
        $extra = array_diff_key($localeData, $baseLocaleData);

        $failures = array();
        if (!empty($missing)) {
            $failures['missing'] = array_keys($missing);
        }
        if (!empty($extra)) {
            $failures['extra'] =  array_keys($extra);
        }
        return $failures;
    }

    /**
     * Test default locale
     *
     * Check that all translation phrases in code are present in the locale files
     *
     * @param string $file
     * @param array $phrases
     *
     * @dataProvider defaultLocaleDataProvider
     */
    public function testDefaultLocale($file, $phrases)
    {
        $failures = $this->comparePhrase($phrases, $this->csvParser->getDataPairs($file));
        $this->assertEmpty(
            $failures,
            $this->printMessage([$file => $failures])
        );
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    public function defaultLocaleDataProvider()
    {
        $parser = $this->prepareParser();
        $parser->parse($this->getI18nPattern());

        $defaultLocale = array();
        foreach ($parser->getPhrases() as $key => $phrase) {
            if (!$phrase->getContextType() || !$phrase->getContextValue()) {
                throw new \RuntimeException(sprintf('Missed context in row #%d.', $key + 1));
            }
            foreach ($phrase->getContextValue() as $context) {
                $phraseText = $this->eliminateSpecialChars($phrase->getPhrase());
                $phraseTranslation = $this->eliminateSpecialChars($phrase->getTranslation());
                $file = $this->buildFilePath($phrase, $context);
                $defaultLocale[$file]['file'] = $file;
                $defaultLocale[$file]['phrases'][$phraseText] = $phraseTranslation;
            }
        }
        return $defaultLocale;
    }

    /**
     * @param \Magento\Tools\I18n\Code\Dictionary\Phrase $phrase
     * @param array $context
     * @return string
     */
    protected function buildFilePath($phrase, $context)
    {
        $path = $this->getContext()->buildPathToLocaleDirectoryByContext($phrase->getContextType(), $context);
        return \Magento\TestFramework\Utility\Files::init()->getPathToSource() . '/'
        . $path . \Magento\Tools\I18n\Code\Locale::DEFAULT_SYSTEM_LOCALE
        . '.' . \Magento\Tools\I18n\Code\Pack\Writer\File\Csv::FILE_EXTENSION;
    }

    /**
     * @return \Magento\Tools\I18n\Code\Context
     */
    protected function getContext()
    {
        if ($this->context === null) {
            $this->context = new \Magento\Tools\I18n\Code\Context();
        }
        return $this->context;
    }

    /**
     * @return array
     */
    protected function getI18nPattern()
    {
        $magentoPath = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        $filesPattern = [
            [
                'type' => 'php',
                'paths' => array($magentoPath . '/app/code/', $magentoPath . '/app/design/'),
                'fileMask' => '/\.(php|phtml)$/'
            ],
            [
                'type' => 'js',
                'paths' => [
                    $magentoPath . '/app/code/',
                    $magentoPath . '/app/design/',
                    $magentoPath . '/lib/web/mage/',
                    $magentoPath . '/lib/web/varien/'
                ],
                'fileMask' => '/\.(js|phtml)$/'
            ],
            [
                'type' => 'xml',
                'paths' => array($magentoPath . '/app/code/', $magentoPath . '/app/design/'),
                'fileMask' => '/\.xml$/'
            ]
        ];

        return $filesPattern;
    }

    /**
     * @return \Magento\Tools\I18n\Code\Parser\Contextual
     */
    protected function prepareParser()
    {
        $filesCollector = new \Magento\Tools\I18n\Code\FilesCollector();

        $phraseCollector = new \Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\PhraseCollector(
            new \Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer()
        );
        $adapters = array(
            'php' => new \Magento\Tools\I18n\Code\Parser\Adapter\Php($phraseCollector),
            'js' =>  new \Magento\Tools\I18n\Code\Parser\Adapter\Js(),
            'xml' => new \Magento\Tools\I18n\Code\Parser\Adapter\Xml()
        );

        $parserContextual = new \Magento\Tools\I18n\Code\Parser\Contextual(
            $filesCollector,
            new \Magento\Tools\I18n\Code\Factory(),
            new \Magento\Tools\I18n\Code\Context()
        );
        foreach ($adapters as $type => $adapter) {
            $parserContextual->addAdapter($type, $adapter);
        }

        return $parserContextual;
    }

    /**
     * @param string $text
     * @return mixed
     */
    protected function eliminateSpecialChars($text)
    {
        return preg_replace(['/\\\\\'/', '/\\\\\\\\/'], ['\'', '\\'], $text);
    }
}
