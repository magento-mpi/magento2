<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

$basePath = realpath(__DIR__ . '/../../../../../');
require_once $basePath . '/app/autoload.php';
require __DIR__ . '/../Layout/Formatter.php';

\Magento\Autoload\IncludePath::addIncludePath(
    array($basePath . '/dev/tests/static/framework', $basePath . '/lib', $basePath . '/dev/tools')
);

try {
    $opt = new Zend_Console_Getopt(
        array('file|f=s' => 'File to process(required)', 'output|o' => 'Output to console')
    );
    $opt->parse();

    if ($opt->file) {
        $classFiles = array($opt->file);
    } else {
        \Magento\TestFramework\Utility\Files::setInstance(new \Magento\TestFramework\Utility\Files($basePath));
        $files = \Magento\TestFramework\Utility\Files::init();
        $classFiles = $files->getClassFiles(true, false, false, false, false, false, false);
    }

    $classFiles = array_filter(
        $classFiles,
        function ($value) {
            return strpos(
                $value,
                'data-install'
            ) === false && strpos(
                $value,
                'data-upgrade'
            ) === false && strpos(
                $value,
                'install-'
            ) === false && strpos(
                $value,
                'upgrade-'
            ) === false;
        }
    );

    $replacer = new \Magento\Tools\Config\Updaters\Replacer($classFiles, $opt->output);

    $replacer->process();

    exit(0);
} catch (Exception $e) {
    echo $e, PHP_EOL;
    exit(255);
}
