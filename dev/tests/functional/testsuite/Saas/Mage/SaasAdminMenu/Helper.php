<?php
/**
 * {license_notice}
 *
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @method string processString processString(string $string)
 * @method array parsePath parsePath(string $path)
 */
class Saas_Mage_SaasAdminMenu_Helper extends Enterprise_Mage_Product_Helper
{
    /**
     * Navigate by menu.
     *
     * Pre condition: An user logged in dashboard. Dashboard is opened.
     * Example: $this->saasAdminMenuHelper()->navigateByMenu('Marketing/Tags');
     *
     * @param string $path
     * @param bool $page
     */
    public function navigateByMenu($path, $page = false)
    {
        $menuItems = $this->parsePath($path);

        $this->clickMainMenuElement($menuItems[0]);

        if (isset($menuItems[1])) {
            $this->clickSubMenuElement($menuItems[1], $page);
        }
    }

    /**
     * Click main menu element in the top navigation.
     *
     * Pre condition: An user logged in dashboard. Dashboard is opened.
     *
     * @param string $title
     */
    public function clickMainMenuElement($title)
    {
        $this->addParameter('menuTitle', $this->processString($title));

        $this->getControlElement('pageelement', 'menu_item')
            ->click();

        $this->waitForElementVisible(
            $this->_getControlXpath('pageelement', 'submenu_container')
        );
    }

    /**
     * Click sub menu element.
     *
     * Pre condition: Submenu container is opened. Parameter 'menuTitle' is set.
     *
     * @param string $title
     * @param bool $page
     */
    public function clickSubMenuElement($title, $page = false)
    {
        $this->_setSubMenuTitleParameter($title);

        $this->getControlElement('pageelement', 'submenu_item')
            ->click();

        $this->waitForPageToLoad();
        if ($page) {
            $this->validatePage($page);
        }
    }

    /**
     * Check if sub menu element is present.
     *
     * Pre condition: Submenu container is opened. Parameter 'menuTitle' is set.
     *
     * @param string $title
     * @return bool
     */
    public function isSubMenuElementPresent($title)
    {
        $this->_setSubMenuTitleParameter($title);

        return $this->controlIsPresent('pageelement', 'submenu_item');
    }

    /**
     * @param string $title
     * @throws InvalidArgumentException
     */
    protected function _setSubMenuTitleParameter($title)
    {
        if (!$this->getParameter('menuTitle')) {
            throw new InvalidArgumentException('Submenu container is not opened or Parameter "menuTitle" is not set.');
        }
        $this->addParameter('subMenuTitle', $this->processString($title));
    }
}
