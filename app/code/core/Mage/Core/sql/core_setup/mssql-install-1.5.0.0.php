<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->query("CREATE FULLTEXT CATALOG ftMagento AS DEFAULT");

$installer->getConnection()->query("
CREATE FUNCTION dbo.regexp
(
    @source     varchar(5000),
    @regexp     varchar(1000),
    @ignorecase bit = 0
)
RETURNS BIT AS
BEGIN
    DECLARE @hr         int
    DECLARE @objRegExp  int
    DECLARE @results    int

    EXEC @hr = sp_OACreate 'VBScript.RegExp', @objRegExp OUTPUT
    IF @hr <> 0 BEGIN
        SET @results = 0
        RETURN @results
    END;

    EXEC @hr = sp_OASetProperty @objRegExp, 'Pattern', @regexp
    IF @hr <> 0 BEGIN
        SET @results = 0
        RETURN @results
    END;

    EXEC @hr = sp_OASetProperty @objRegExp, 'Global', false
    IF @hr <> 0 BEGIN
        SET @results = 0
        RETURN @results
    END;

    EXEC @hr = sp_OASetProperty @objRegExp, 'IgnoreCase', @ignorecase
    IF @hr <> 0 BEGIN
        SET @results = 0
        RETURN @results
    END;

    EXEC @hr = sp_OAMethod @objRegExp, 'Test', @results OUTPUT, @source
    IF @hr <> 0 BEGIN
        SET @results = 0
        RETURN @results
    END;

    EXEC @hr = sp_OADestroy @objRegExp
    IF @hr <> 0 BEGIN
        SET @results = 0
        RETURN @results
    END;

    RETURN @results;
END;
");



$installer->getConnection()->query("
    CREATE FUNCTION dbo.regexp_replace
    (
        @source		varchar(max),
        @pattern	varchar(max),
        @replacement	varchar(max),
        @ignorecase bit = 0
    )
    RETURNS VARCHAR(8000) AS
    BEGIN
        DECLARE @hr         int
        DECLARE @objRegExp  int
        DECLARE @results    varchar(8000)

        EXEC @hr = sp_OACreate 'VBScript.RegExp', @objRegExp OUTPUT
        IF @hr <> 0 BEGIN
            SET @results = ''
            RETURN @results
        END;

        EXEC @hr = sp_OASetProperty @objRegExp, 'Pattern', @pattern
        IF @hr <> 0 BEGIN
            SET @results = ''
            RETURN @results
        END;

        EXEC @hr = sp_OASetProperty @objRegExp, 'Global', 1
        IF @hr <> 0 BEGIN
            SET @results = ''
            RETURN @results
        END;

            EXEC @hr= sp_OASetProperty @objRegExp, 'MultiLine', 1
        IF @hr <> 0 BEGIN
            SET @results = ''
            RETURN @results
        END;


        EXEC @hr = sp_OASetProperty @objRegExp, 'IgnoreCase', @ignorecase
        IF @hr <> 0 BEGIN
            SET @results = ''
            RETURN @results
        END;

        EXEC @hr = sp_OAMethod @objRegExp, 'Replace', @results OUTPUT,
                    @source, @replacement

        IF @hr <> 0 BEGIN
            SET @results = ''
            RETURN @results
        END;

        EXEC @hr = sp_OADestroy @objRegExp
        IF @hr <> 0 BEGIN
            SET @results = ''
            RETURN @results
        END;

        RETURN @results;
    END;
");

$installer->getConnection()->query("
CREATE FUNCTION dbo.find_in_set
(
    @needle     varchar(255),
    @haystack   varchar(255)
) RETURNS BIT AS
BEGIN
    DECLARE @found bit;
    SET @found = 0;

    IF (CHARINDEX(',', @haystack) > 0)
        BEGIN
            DECLARE @like_1 varchar(255), @like_2 varchar(255), @like_3 varchar(255);
            SET @like_1 = @needle + ',%';
            SET @like_2 = '%,' + @needle + ',%';
            SET @like_3 = '%,' + @needle;
            IF (@haystack LIKE @like_1 OR @haystack LIKE @like_2 OR @haystack LIKE @like_3)
                SET @found = 1;
            END;
    ELSE
        BEGIN
            IF (@haystack = @needle)
                SET @found = 1;
        END;
    RETURN @found;
END;
");

$installer->getConnection()->query("
CREATE FUNCTION dbo.date_format(
    @Datetime DATETIME,
    @FormatMask VARCHAR(64)
) RETURNS VARCHAR(64) AS
BEGIN
    DECLARE @result VARCHAR(64), @date varchar(20)
    SET @date = CONVERT(VARCHAR(20), CAST(@Datetime as datetime), 120)
    SET @result = @FormatMask

    SET @result = REPLACE(@result, '%Y', SUBSTRING(@date, 1, 4))
    SET @result = REPLACE(@result, '%m', SUBSTRING(@date, 6, 2))
    SET @result = REPLACE(@result, '%d', SUBSTRING(@date, 9, 2))
    SET @result = REPLACE(@result, '%H', SUBSTRING(@date, 12, 2))
    SET @result = REPLACE(@result, '%i', SUBSTRING(@date, 15, 2))
    SET @result = REPLACE(@result, '%s', SUBSTRING(@date, 18, 2))
    RETURN @result
END;
");

$installFile = dirname(__FILE__) . DS . 'install-1.5.0.0.php';
if (file_exists($installFile)) {
    include $installFile;
}
