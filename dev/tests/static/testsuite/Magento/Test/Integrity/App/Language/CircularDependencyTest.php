<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\App\Language;

class CircularDependencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test circular dependencies between languages
     */
    public function testCircularDependencies()
    {
        $package = new Package();

        $rootDirectory = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        $declaredLanguages = $package->readDeclarationFiles($rootDirectory);

        $dictionary = new \Magento\Framework\App\Language\Dictionary($this->getFileSystem($rootDirectory));

        foreach ($declaredLanguages as $language) {
            /** Get dictionary by language code */
            try {
                $dictionary->getDictionary($language[2]);
            } catch (\LogicException $e) {
                $this->fail($e->getMessage());
            }
        }
    }

    /**
     * @param string $rootDirectory
     * @return \Magento\Framework\App\Filesystem
     */
    protected function getFileSystem($rootDirectory)
    {
        return new \Magento\Framework\App\Filesystem(
            new \Magento\Framework\App\Filesystem\DirectoryList($rootDirectory),
            new \Magento\Framework\Filesystem\Directory\ReadFactory(),
            new \Magento\Framework\Filesystem\Directory\WriteFactory()
        );
    }
}
