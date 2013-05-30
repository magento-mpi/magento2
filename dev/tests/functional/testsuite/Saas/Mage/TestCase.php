<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Mage_TestCase extends Mage_Selenium_TestCase
{
    /**
     * Name of the first page after logging into the back-end
     * @var string
     */
    protected $_pageAfterAdminLogin = 'store_launcher';

    /**
     * Path separator
     */
    const PATH_SEPARATOR = '/';

    /**
     * @param string $string
     * @return string
     * @throws InvalidArgumentException
     */
    public function processString($string)
    {
        $string = trim($string);
        if (!$string) {
            throw new InvalidArgumentException('Empty parameter is passed.');
        }

        return $string;
    }

    /**
     * @param string $path
     * @return array
     */
    public function parsePath($path)
    {
        return array_map('trim', explode(self::PATH_SEPARATOR, $this->processString($path)));
    }

    /**
     * @param string $header
     * @return bool
     */
    public function isHeaderPresent($header)
    {
        $this->addParameter('headerText', $this->processString($header));

        return $this->controlIsPresent('pageelement', 'header_main');
    }
}
