<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Test\Legacy\Magento\Framework\ObjectManager;

class DiConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testObsoleteDiFormat()
    {
        $invoker = new \Magento\Framework\Test\Utility\AggregateInvoker($this);
        $invoker(
            [$this, 'assertObsoleteFormat'],
            \Magento\Framework\Test\Utility\Files::init()->getDiConfigs(true)
        );
    }

    /**
     * Scan the specified di.xml file and assert that it has no obsolete nodes
     *
     * @param string $file
     */
    public function assertObsoleteFormat($file)
    {
        $xml = simplexml_load_file($file);
        $this->assertSame(
            [],
            $xml->xpath('//param'),
            'The <param> node is obsolete. Instead, use the <argument name="..." xsi:type="...">'
        );
        $this->assertSame(
            [],
            $xml->xpath('//instance'),
            'The <instance> node is obsolete. Instead, use the <argument name="..." xsi:type="object">'
        );
        $this->assertSame(
            [],
            $xml->xpath('//array'),
            'The <array> node is obsolete. Instead, use the <argument name="..." xsi:type="array">'
        );
        $this->assertSame(
            [],
            $xml->xpath('//item[@key]'),
            'The <item key="..."> node is obsolete. Instead, use the <item name="..." xsi:type="...">'
        );
        $this->assertSame(
            [],
            $xml->xpath('//value'),
            'The <value> node is obsolete. Instead, provide the actual value as a text literal.'
        );
    }
}
