<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Parser;

/**
 * XML Parser for Themes
 */
class ThemeXmlParser extends XmlParserAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getSubPath()
    {
        return '/theme.xml';
    }

    /**
     * {@inheritdoc}
     */
    protected function parseMappings()
    {
        /** @var $package \SimpleXMLElement */
        $package = simplexml_load_file($this->getFile()->getPathname());
        $packageName = basename($this->_rootDir . $this->_componentDir);
        $vendorName = basename(dirname($this->_rootDir . $this->_componentDir));
        $definitions = array();
        if (isset($package)) {
            $map = array();
            $name = (string)$vendorName . '_' . $packageName;
            //Dependencies
            $dependency = $package->xpath("parent");

            if (!empty($dependency)) {
                $depName = (String)$dependency[0] . "-Theme";
                $map[$depName] = $depName;
            }
            $definitions = $this->createDefinition(
                (string)$name . "-Theme",
                '0.1.0',
                $this->getComponentDir(),
                $map
            );
        }

        return $definitions;
    }
}
