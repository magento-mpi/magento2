<?php
/**
 * Test layout declaration and usage of block elements
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Layout;

class BlocksTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected static $_containerAliases = array();

    /**
     * @var array
     */
    protected static $_blockAliases = array();

    /**
     * Collect declarations of containers per layout file that have aliases
     */
    public static function setUpBeforeClass()
    {
        foreach (\Magento\Framework\Test\Utility\Files::init()->getLayoutFiles(array(), false) as $file) {
            $xml = simplexml_load_file($file);
            $elements = $xml->xpath('/layout//*[self::container or self::block]') ?: array();
            /** @var $node \SimpleXMLElement */
            foreach ($elements as $node) {
                $alias = (string)$node['as'];
                if (empty($alias)) {
                    $alias = (string)$node['name'];
                }
                if ($node->getName() == 'container') {
                    self::$_containerAliases[$alias]['files'][] = $file;
                    self::$_containerAliases[$alias]['names'][] = (string)$node['name'];
                } else {
                    self::$_blockAliases[$alias]['files'][] = $file;
                    self::$_blockAliases[$alias]['names'][] = (string)$node['name'];
                }
            }
        }
    }

    public function testBlocksNotContainers()
    {
        $invoker = new \Magento\Framework\Test\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * Check that containers are not used as blocks in templates
             *
             * @param string $alias
             * @param string $file
             * @throws \Exception|PHPUnit_Framework_ExpectationFailedException
             */
            function ($alias, $file) {
                if (isset(self::$_containerAliases[$alias])) {
                    if (!isset(self::$_blockAliases[$alias])) {
                        $this->fail(
                            "Element with alias '{$alias}' is used as a block in file '{$file}' ".
                            "via getChildBlock() method," .
                            " while '{$alias}' alias is declared as a container in file(s): " .
                            join(
                                ', ',
                                self::$_containerAliases[$alias]['files']
                            )
                        );
                    } else {
                        $this->markTestIncomplete(
                            "Element with alias '{$alias}' is used as a block in file '{$file}' ".
                            "via getChildBlock() method." .
                            " It's impossible to determine explicitly whether the element is a block or a container, " .
                            "as it is declared as a container in file(s): " .
                            join(
                                ', ',
                                self::$_containerAliases[$alias]['files']
                            ) . " and as a block in file(s): " . join(
                                ', ',
                                self::$_blockAliases[$alias]['files']
                            )
                        );
                    }
                }
            },
            $this->getChildBlockDataProvider()
        );
    }

    /**
     * @return array
     */
    public function getChildBlockDataProvider()
    {
        $result = array();
        foreach (\Magento\Framework\Test\Utility\Files::init()->getPhpFiles(true, false, true, false) as $file) {
            $aliases = \Magento\Framework\Test\Utility\Classes::getAllMatches(
                file_get_contents($file),
                '/\->getChildBlock\(\'([^\']+)\'\)/x'
            );
            foreach ($aliases as $alias) {
                $result[$file] = array($alias, $file);
            }
        }
        return $result;
    }
}
