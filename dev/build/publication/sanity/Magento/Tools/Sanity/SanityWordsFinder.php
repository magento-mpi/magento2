<?php
/**
 * Service routines for sanity check command line script
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Sanity;

/**
 * Extend words finder class, which is designed for sanity tests. The added functionality is method to search through
 * directories and method to return words list for logging.
 */
class SanityWordsFinder extends \Magento\TestFramework\Inspection\WordsFinder
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    protected $copyrightSkipList = [
        'app/code/Magento/Doc/view/doc/web/jumly',
        'app/code/Magento/Fedex/etc/wsdl',
        'dev/tests/integration/testsuite/Magento/Framework/Css/PreProcessor/_files',
        'dev/tests/integration/testsuite/Magento/Framework/Less/_files/design/frontend/test_pre_process',
        'dev/tests/integration/testsuite/Magento/Framework/Less/_files/lib/web/magento_import.less',
        'dev/tests/integration/testsuite/Magento/Framework/Less/_files/lib/web/some_dir',
        'dev/tests/integration/testsuite/Magento/Core/Model/_files/design/frontend/test_default/web/result_source.css',
        'dev/tests/integration/testsuite/Magento/Core/Model/_files/design/frontend/test_default/web/result_source_dev.css',
        'dev/tests/integration/testsuite/Magento/Core/Model/_files/design/frontend/test_default/web/source.less',
        'dev/tests/integration/tmp',
        'dev/tests/js/framework/qunit',
        'dev/tests/static/report',
        'dev/tests/static/framework/Magento/Sniffs/Annotations',
        'dev/tests/static/testsuite/Magento/Test/Php/Exemplar/_files/phpcs',
        'dev/tests/static/testsuite/Magento/Test/Php/Exemplar/_files/phpmd/input',
        'dev/tests/static/testsuite/Magento/Test/Php/Exemplar/_files/phpmd_ruleset.xsd',
        'dev/tests/static/testsuite/Magento/Test/Php/Exemplar/CodeMessTest/phpmd/input',
        'dev/tests/static/testsuite/Magento/Test/Php/Exemplar/CodeStyleTest/phpcs/input',
        'dev/tests/static/testsuite/Magento/Test/Php/Exemplar/CodeStyleTest/phpcs/expected',
        'dev/tests/static/testsuite/Magento/Test/Php/Exemplar/CodeMessTest/phpmd_ruleset.xsd',
        'dev/tests/unit/testsuite/Magento/Tools/View/Generator/_files/ThemeDeployment/run/source',
        'dev/tools/PHP-Parser',
        'Gruntfile.js',
        'lib/internal/Apache',
        'lib/internal/CardinalCommerce',
        'lib/internal/Credis',
        'lib/internal/Cm',
        'lib/internal/JSMin',
        'lib/internal/Less',
        'lib/internal/PEAR',
        'lib/internal/phpseclib',
        'lib/web/dnd.js',
        'lib/web/extjs',
        'lib/web/firebug',
        'lib/web/flash',
        'lib/web/headjs',
        'lib/web/jquery',
        'lib/web/ko',
        'lib/web/moment.js',
        'lib/web/lib',
        'lib/web/mage/adminhtml/hash.js',
        'lib/web/matchMedia.js',
        'lib/web/modernizr',
        'lib/web/prototype',
        'lib/web/requirejs',
        'lib/web/scriptaculous',
        'lib/web/selectivizr.js',
        'lib/web/tiny_mce',
        'lib/web/underscore.js',
        'pub/media',
        'var',
        'setup/pub/bootstrap',
        'setup/pub/angular',
        'setup/pub/angular-ui-bootstrap',
        'setup/pub/angular-ui-router',
        'setup/pub/angular-ng-storage',
        'setup/vendor',
        'dev/tests/unit/testsuite/Magento/Tools/Migration/Acl/_files/log/AclXPathToAclId.log',
        'dev/tools/Magento/Tools/StaticReview',
        'dev/tools/Magento/Tools/psr',
        'dev/tests/functional/composer.json.dist',
        'dev/tests/integration/framework/tests/unit/testsuite/Magento/Test/Bootstrap/_files/0',
        'dev/tests/unit/testsuite/Magento/_files/empty_definition_file',
        'dev/tests/unit/testsuite/Magento/_files/test_definition_file',
    ];

    /**
     * @param string|array $configFiles
     * @param string $baseDir
     * @param bool $isCopyrightChecked
     * @throws \Magento\TestFramework\Inspection\Exception
     */
    public function __construct($configFiles, $baseDir, $isCopyrightChecked = true)
    {
        parent::__construct($configFiles, $baseDir, $isCopyrightChecked);
    }

    /**
     * Get list of words, configured to be searched
     *
     * @return array
     */
    public function getSearchedWords()
    {
        return $this->_words;
    }

    /**
     * Search words in files content recursively within base directory tree
     *
     * @return array
     */
    public function findWordsRecursively()
    {
        return $this->_findWordsRecursively($this->_baseDir);
    }

    /**
     * Search words in files content recursively within base directory tree
     *
     * @param  string $currentDir Current dir to look in
     * @return array
     */
    protected function _findWordsRecursively($currentDir)
    {
        $result = [];

        $entries = glob($currentDir . '/*');
        $initialLength = strlen($this->_baseDir);
        foreach ($entries as $entry) {
            if (is_file($entry)) {
                $foundWords = $this->findWords($entry);
                if (!$foundWords) {
                    continue;
                }
                $relPath = substr($entry, $initialLength + 1);
                $result[] = ['words' => $foundWords, 'file' => $relPath];
            } elseif (is_dir($entry)) {
                $more = $this->_findWordsRecursively($entry);
                $result = array_merge($result, $more);
            }
        }

        return $result;
    }
}
