<?php
/**
 * Integrity test for configuration (config.xml)
 *
 * {license_notice}
 *
 * @category    tests
 * @package     integration
 * @subpackage  integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * The node 'config/global/fieldsets' had been relocated from config.xml to fieldset.xml
     *
     * @param string $path
     * @dataProvider fieldsetsRemovalDataProvider
     */
    public function testFieldsetsRemoval($path)
    {
        $this->assertFalse(Mage::getConfig()->getNode($path));
        $config = new Mage_Core_Model_Config_Fieldset;
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $config->getNode($path));
    }

    public function fieldsetsRemovalDataProvider()
    {
        return array(array('global/fieldsets'), array('admin/fieldsets'));
    }

    /**
     * The "deprecated node" feature had been removed
     */
    public function testDeprecatedNodeRemoval()
    {
        $this->assertSame(array(), Mage::getConfig()->getNode()->xpath('/config/global/models/*/deprecatedNode'));
    }

    /**
     * Verification that there no table definition in configuration
     */
    public function testTableDefinitionRemoval()
    {
        $this->assertSame(array(), Mage::getConfig()->getNode()->xpath('/config/global/models/*/entities/*/table'));
    }

    /**
     * @dataProvider classPrefixRemovalDataProvider
     */
    public function testClassPrefixRemoval($classType)
    {
        $this->assertSame(array(), Mage::getConfig()->getNode()->xpath("/config/global/{$classType}s/*/class"));
    }

    public function classPrefixRemovalDataProvider()
    {
        return array(
            array('model'),
            array('helper'),
            array('block'),
        );
    }

    public function testResourceModelMappingRemoval()
    {
        $this->assertSame(array(), Mage::getConfig()->getNode()->xpath('/config/global/models/*/resourceModel'));
    }
}