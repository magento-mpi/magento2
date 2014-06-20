<?php
/**
 * Find "install_wizard.xml" file and validate
 *
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

    protected function setUp()
    {
        $this->csvParser = new \Magento\Framework\File\Csv();
        $this->csvParser->setDelimiter(',');
    }

    /**
     * @param string $modulePath
     * @dataProvider getModulesPath
     */
    public function testCoincidenceNonEnglishFiles($modulePath)
    {
        $files = [];
        foreach (glob("{$modulePath}/i18n/*.csv") as $file) {
            $locale = str_replace('.csv', '', basename($file));
            $files[$locale] = $file;
        }

        $failures = $this->checkModuleFiles($files);

        $this->assertEmpty(
            $failures,
            $this->printMessage($failures)
        );
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
                foreach (array_keys($error) as $phrase) {
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
            return ["{$this->baseLocale}.csv file is not found"];
        }
        $baseLocaleData = $this->csvParser->getDataPairs($files[$this->baseLocale]);
        foreach ($files as $locale => $file) {
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
            $failures['missing'] = $missing;
        }
        if (!empty($extra)) {
            $failures['extra'] = $extra;
        }
        return $failures;
    }
}
