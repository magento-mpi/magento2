<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\App\Language;

class TranslationFilesNonEnglishTest extends TranslationFiles
{
    /**
     * @var string
     */
    protected $defaultLocale = \Magento\Tools\I18n\Code\Locale::DEFAULT_SYSTEM_LOCALE;

    /**
     * Checked whether all the phrases from en_US.csv file is present in all other locale csv files,
     * and whether there is obsolete
     *
     * @param string $placePath
     * @dataProvider getLocalePlacePath
     */
    public function testCoincidenceNonEnglishFiles($placePath)
    {
        $this->markTestSkipped('MAGETWO-26083');
        $files = $this->getCsvFiles($placePath);

        $failures = array();
        if (!empty($files)) {
            $failures = $this->checkModuleFiles($files);
        }

        $this->assertEmpty(
            $failures,
            $this->printMessage(
                $failures,
                'Found discrepancy between default locale and other locale'
            )
        );
    }

    /**
     * @param string[][] $files Array csv files in format $files[$locale][$filesPath]
     * @return array
     */
    protected function checkModuleFiles($files)
    {
        $failures = [];
        if (!isset($files[$this->defaultLocale])) {
            $failures[$this->defaultLocale]['missing'] = ["{$this->defaultLocale}.csv file is not found"];
            return $failures;
        }
        $baseLocaleData = $this->csvParser->getDataPairs($files[$this->defaultLocale]);
        foreach ($this->getDeclaredLangs() as $locale) {
            if (!isset($files[$locale])) {
                $failures[$locale]['missing'] = ["{$locale}.csv file is not found"];
                continue;
            }
            $localeFailures = $this->comparePhrase($baseLocaleData, $this->csvParser->getDataPairs($files[$locale]));
            if (!empty($localeFailures)) {
                $failures[$locale] = $localeFailures;
            }
            unset($files[$locale]);
        }
        foreach (array_keys($files) as $locale) {
            $failures[$locale]['extra'] = ["{$locale}.csv file is present but {$locale} locale is not declared"];
        }
        return $failures;
    }

    /**
     * Scan code base for language.xml files and figure out distinct list of languages from their file structure
     *
     * @return array
     */
    protected function getDeclaredLangs()
    {
        $pathToSource = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        $result = [];
        foreach (Package::readDeclarationFiles($pathToSource) as $row) {
            $result[$row[2]] = $row[2];
        }
        return $result;
    }
}
