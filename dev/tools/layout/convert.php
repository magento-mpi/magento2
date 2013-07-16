<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

error_reporting(E_ALL);
$rootDir = str_replace('\\', '/', realpath(__DIR__ . '/../../..'));

require $rootDir . '/app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(__DIR__);

$template = <<<XML
<?xml version="1.0"?>
<!--
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
-->
<layout version="0.1.0">%s</layout>
XML;

try {
    /* ---Boilerplating---- */
    // List of layout files patterns, relative to layout directories
    $relativePatterns = array(
        '*.xml',
        /* And here is the hardcoded list of files, that are not located right in the layout directory */
        // Adminhtml
        'system/design/design.xml',
        'system/store/store.xml',
        'system/email/template.xml',
        'system/account.xml', // Same file in Saas_Backend
    );

    // General helper
    $layoutHelper = new Layout_Helper;

    // Module layout files
    $moduleFilesIterator = new Layout_FileIterator_Verified(
        new IteratorIterator(new Layout_FileIterator_Source($rootDir . '/app/code/*/*/view/*', $relativePatterns))
    );
    $moduleGroupedIterator = new Layout_FileIterator_Grouped_ByModuleDirectory($moduleFilesIterator, $layoutHelper);

    // Theme layout files, ordered for parent theme files to come before child theme files
    $themesFilesIterator = new Layout_FileIterator_Verified(
        new IteratorIterator(new Layout_FileIterator_Source($rootDir . '/app/design/*/*/*/*', $relativePatterns))
    );
    $themeReader = new Theme_Reader($rootDir);
    $layoutInheritance = new Layout_Inheritance($rootDir, $relativePatterns, $themeReader);
    $themesFilesOrderedIterator = new Layout_FileIterator_ByThemeInheritance($themesFilesIterator, $themeReader);
    $themeGroupedIterator = new Layout_FileIterator_Grouped_ByModuleDirectory($themesFilesOrderedIterator,
        $layoutHelper);
    $themeWithInheritanceIterator = new Layout_FileIterator_Grouped_WithInheritance($themeGroupedIterator,
        $layoutInheritance, $layoutHelper);

    // Aggregated iterator to iterate over layout files
    $layoutGroupIterator = new AppendIterator();
    $layoutGroupIterator->append(new IteratorIterator($moduleGroupedIterator));
    $layoutGroupIterator->append(new IteratorIterator($themeWithInheritanceIterator));

    // Configure final object, which separates handles into files
    $layoutAnalyzer = new Layout_Analyzer(new Layout_Merger(), new Xml_Formatter('    '), $template);
    $handleSeparator = new Layout_Handle_Separator($layoutGroupIterator, $layoutAnalyzer, $layoutInheritance);

    // Configure the object to delete all the old layout files
    $oldLayoutFilesIterator = new AppendIterator();
    $oldLayoutFilesIterator->append(new IteratorIterator($moduleFilesIterator));
    $oldLayoutFilesIterator->append(new IteratorIterator($themesFilesIterator));
    $removal = new Files_Removal($oldLayoutFilesIterator);

    /* Run it */
    $handleSeparator->performTheJob();
    $removal->run();

    echo "Completed!\n";
    echo 'Do not forget to manually fix handles from app/code/Mage/Install/view/install/layout_*.xml';
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
