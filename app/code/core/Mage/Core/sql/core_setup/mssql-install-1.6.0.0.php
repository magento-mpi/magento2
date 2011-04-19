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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
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
        @source        varchar(max),
        @pattern    varchar(max),
        @replacement    varchar(max),
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

$installer->getConnection()->query("
create procedure [dbo].[get_table_fk] (@objectName varchar(128)) as
begin
declare @objectId int;
declare @drop varchar(MAX) = '';
declare @columns varchar(MAX) = ''
declare @primaryKeyName varchar(MAX) = ''
declare @primaryKeyColumns varchar(MAX) = ''
declare @indexes varchar(MAX) = ''
declare @fkeys varchar(MAX) = ''
declare @table varchar(128)
declare @ddl varchar(MAX)

set @objectId = object_id(@objectName)
if (@objectId is null)
    raiserror 99999 'Invalid table name';

select @fkeys = @fkeys +
    'ALTER TABLE ' + object_name(@objectId) + ' ADD CONSTRAINT ' + object_name(fkc.constraint_object_id) + ' FOREIGN KEY (' +
 stuff((
    select ', ' + '['+ c1.name +']'
    from sys.all_columns c1
    where c1.object_id = fkc.parent_object_id
        and c1.column_id = fkc.parent_column_id
   for xml path(''), type
  ).value('.','varchar(8000)'), 1,2,'' ) + ')' + ' REFERENCES [' + object_name(fkc.referenced_object_id) + '] (' +
 stuff((
    select ', ' + '['+ c1.name +']'
    from sys.all_columns c1
    where c1.object_id = fkc.referenced_object_id
        and c1.column_id = fkc.referenced_column_id
   for xml path(''), type
  ).value('.','varchar(8000)'), 1,2,'' ) + ');' + char(13)
from sys.foreign_key_columns fkc
where fkc.parent_object_id = @objectId

select @fkeys as ddl_script

end");

$installer->getConnection()->query("
    CREATE  procedure [dbo].[get_table_dll] (@objectName varchar(128), @withfk int, @withdrop int) as
    begin
declare @objectId int;
declare @drop varchar(MAX) = '';
declare @columns varchar(MAX) = ''
declare @primaryKeyName varchar(MAX) = ''
declare @primaryKeyColumns varchar(MAX) = ''
declare @indexes varchar(MAX) = ''
declare @fkeys varchar(MAX) = ''
declare @table varchar(128)
declare @ddl varchar(MAX)

set @objectId = object_id(@objectName)
if (@objectId is null)
    raiserror 99999 'Invalid table name';

if (@withdrop = 1)
    select @drop = 'IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = ' + cast(@objectId as varchar) + ' AND type in (N''U''))' + char(13) +
    'DROP TABLE [' + @objectName + ']'

select @table = '[' + object_name(@objectId) + ']'
select
    @columns = @columns  + char(32) + '[' + c.name + '] ' +
        case
            when type_name(c.system_type_id) like '%char' then type_name(c.system_type_id) + '(' + cast(c.max_length as varchar(5)) + ')'
            else type_name(c.system_type_id)
        end +
        case
            when c.is_nullable = 0 then ' NOT NULL'
            else ''
        end    +
        case
            when c.is_identity = 1 then ' IDENTITY(1, 1)'
            else ''
        end
          + ISNULL(' DEFAULT ' + dc.definition, '')
         + ', ' + char(13),
    @primaryKeyName =
        case
            when ic.column_id is not null  then ic.name
            else @primaryKeyName
        end,
 @primaryKeyColumns = stuff((
    select ', ' + '['+ c1.name +']'
    from sys.all_columns c1
    inner join sys.index_columns ic1 on ic1.object_id = c1.object_id
        and ic1.column_id = c1.column_id
    inner join sys.indexes ix1 on ix1.object_id = ic1.object_id
        and ix1.object_id = c1.object_id
        and ix1.index_id = ic1.index_id
    where c1.object_id = c.object_id
        and ix1.is_primary_key = 1
   for xml path(''), type
  ).value('.','varchar(8000)'), 1,2,'' )
from sys.all_columns c
left join (
    select sic.column_id, sic.object_id, si.name
    from sys.index_columns sic
    inner join sys.indexes si on si.object_id = sic.object_id
        and si.index_id = sic.index_id
        and si.is_primary_key = 1
    ) ic on ic.object_id = c.object_id
    and ic.column_id = c.column_id
left join sys.default_constraints dc on dc.parent_object_id = c.object_id
    and dc.parent_column_id = c.column_id
where c.object_id = @objectId
order by c.column_id


select
    @indexes = @indexes +
    case
        when ix.is_unique = 1 then 'CREATE UNIQUE INDEX '
        else 'CREATE INDEX '
    end +
    isnull('['+max(ix.name)+']','') + ' ON ' + object_name(@objectId) + '(' +
 stuff((
    select ', ' + '['+ c1.name +']'
    from sys.all_columns c1
    inner join sys.index_columns ic1 on ic1.object_id = c1.object_id
        and ic1.column_id = c1.column_id
    inner join sys.indexes ix1 on ix1.object_id = ic1.object_id
        and ix1.object_id = c1.object_id
        and ix1.index_id = ic1.index_id
    where ix1.object_id = ix.object_id
        and ix1.index_id = ix.index_id
   for xml path(''), type
  ).value('.','varchar(8000)'), 1,2,'' ) + '); ' + char(13)
from sys.all_columns c
left join sys.index_columns ic on ic.object_id = c.object_id
    and ic.column_id = c.column_id
left join sys.indexes ix on ix.object_id = ic.object_id
    and ix.object_id = c.object_id
    and ix.index_id = ic.index_id
where c.object_id = @objectId
and ix.is_primary_key = 0
group by ix.object_id, ix.index_id, ix.is_unique


if (@withfk = 1)
select @fkeys = @fkeys +
    'ALTER TABLE ' + object_name(@objectId) + ' ADD CONSTRAINT ' + object_name(fkc.constraint_object_id) + ' FOREIGN KEY (' +
 stuff((
    select ', ' + '['+ c1.name +']'
    from sys.all_columns c1
    where c1.object_id = fkc.parent_object_id
        and c1.column_id = fkc.parent_column_id
   for xml path(''), type
  ).value('.','varchar(8000)'), 1,2,'' ) + ')' + ' REFERENCES [' + object_name(fkc.referenced_object_id) + '] (' +
 stuff((
    select ', ' + '['+ c1.name +']'
    from sys.all_columns c1
    where c1.object_id = fkc.referenced_object_id
        and c1.column_id = fkc.referenced_column_id
   for xml path(''), type
  ).value('.','varchar(8000)'), 1,2,'' ) + ');' + char(13)
from sys.foreign_key_columns fkc
where fkc.parent_object_id = @objectId

select @ddl = @drop + char(13) + char(13) + 'CREATE TABLE ' + @table + ' ('+ char(13) + (substring(@columns, 1, len(@columns) - 3)) +
    isnull(', ' + char(13) + char(32) + 'CONSTRAINT [' + @primaryKeyName + '] PRIMARY KEY (' + @primaryKeyColumns + ')', '')
+char(13)+');' + char(13) + char(13) + @indexes + char(13) + @fkeys

select @ddl as ddl_script
end");

$installFile = dirname(__FILE__) . DS . 'install-1.6.0.0.php';
if (file_exists($installFile)) {
    include $installFile;
}
