<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity;
use \Magento\TestFramework\Utility\Files;

class AdminhtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider decouplingDataProvider
     *
     * @param $file
     */
    public function testAdminhtmlDecoupling($file)
    {
        $blackList = $this->_getDecouplingBlackList();
        $blackList = array_map(
            function ($element) {
                $element = str_replace('/', DIRECTORY_SEPARATOR, $element);
                return preg_quote($element, '/');
            },
            $blackList
        );
        $this->assertRegExp('/(' . implode('|', $blackList) . ')/', $file);
    }

    /**
     * @return array
     */
    public function decouplingDataProvider()
    {
        $pathToModule = Files::init()->getPathToSource()
            . DIRECTORY_SEPARATOR . 'app'
            . DIRECTORY_SEPARATOR . 'code'
            . DIRECTORY_SEPARATOR . 'Magento'
            . DIRECTORY_SEPARATOR . 'Adminhtml'
        ;

        $result = glob(
            $pathToModule . DIRECTORY_SEPARATOR . '{Block,Controller,Helper,Model}'. DIRECTORY_SEPARATOR . '*',
            GLOB_BRACE | GLOB_NOSORT
        );
        // append views
        $result = array_merge($result, glob(
            $pathToModule . DIRECTORY_SEPARATOR . 'view[^layout]'
                . DIRECTORY_SEPARATOR . 'adminhtml' . DIRECTORY_SEPARATOR . '*',
            GLOB_BRACE | GLOB_NOSORT
        ));
        // append layouts
        $result = array_merge($result, glob(
            $pathToModule . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'adminhtml'
                . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . '*',
            GLOB_BRACE | GLOB_NOSORT
        ));

        return Files::composeDataSets($result);
    }

    /**
     * @return array
     */
    protected function _getDecouplingBlackList()
    {
        return require __DIR__ . DIRECTORY_SEPARATOR
            . '_files' . DIRECTORY_SEPARATOR
            . 'blacklist' . DIRECTORY_SEPARATOR
            . 'adminhtml_decoupling.php';
    }
}
