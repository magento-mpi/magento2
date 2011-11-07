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

$installer->getConnection()->query("
    create or replace and compile java source named STRING_FUNCTION as
    public class StringFuctions
    {
        public static void entry()
        {

        }
        public static int findInSet(String whatSearch, String whereSearch )
        {
            if (whereSearch.length() == 0) {
                return 0;
            }
            String separator = \",\";
            String[] searchValues = whereSearch.split(separator);
            for(int i = 0; i < searchValues.length ; i++) {
                if (whatSearch.compareTo(searchValues[i]) == 0) {
                    return 1;
                }
            }
            return 0;
        }
    }
");


$installer->getConnection()->query("
   create or replace function checksum_string( p_buff in clob ) return number
   is
      l_sum       number default 0;
      l_n         number;
      l_nu        number;
      l_nl        number;
   begin
      for i in 1 .. trunc(length(p_buff||'x')/2) loop
         if ascii(substr(p_buff||'x', 1+(i-1)*2, 1))>255 then
            --2byte char + (1byte char or 2byte char)
            l_nu:=0;
            l_nl:=ascii(substr(p_buff||'x', 1+(i-1)*2, 1));
            l_n := l_nl;
            l_sum := mod( l_sum+l_n, 4294967296);

            if ascii(substr(p_buff||'x', 2+(i-1)*2, 1))>255 then
               --2byte char + 2byte char
               l_nu:=0;
               l_nl:=ascii(substr(p_buff||'x', 2+(i-1)*2, 1));
               l_n := l_nl;
               l_sum := mod( l_sum+l_n, 4294967296);
            else
               --2byte char + 1byte char
               l_nu:=ascii(substr(p_buff||'x', 2+(i-1)*2, 1));
               l_nl:=ascii('x');
               l_n := l_nu*256 + l_nl;
               l_sum := mod( l_sum+l_n, 4294967296);
            end if;

            elsif ascii(substr(p_buff||'x', 2+(i-1)*2, 1))>255 then
               --1byte char + 2byte char
               l_nu:=0;
               l_nl:=ascii(substr(p_buff||'x', 2+(i-1)*2, 1));
               l_n := l_nl;
               l_sum := mod( l_sum+l_n, 4294967296);

               l_nu:=ascii('x');
               l_nl:=ascii(substr(p_buff||'x', 2+(i-1)*2, 1));
               l_n := l_nu*256 + l_nl;
               l_sum := mod( l_sum+l_n, 4294967296);

            else
               --1byte char + 1byte char
               l_nu:=ascii(substr(p_buff||'x', 1+(i-1)*2, 1));
               l_nl:=ascii(substr(p_buff||'x', 2+(i-1)*2, 1));
               l_n := l_nu*256 + l_nl;
               l_sum := mod( l_sum+l_n, 4294967296);

               -- dbms_output.put_line('l_n : '||l_n);
            end if;
         end loop;

         -- dbms_output.put_line('l_sum : '||l_sum);

         while ( l_sum > 65536 ) loop
            -- l_sum := bitand( l_sum, 65535 ) + trunc(l_sum/65536);
            l_sum := mod( l_sum, 65536 ) + trunc(l_sum/65536);
         end loop;
      return l_sum;
   end checksum_string;
   ");

$installer->getConnection()->query("
    CREATE OR REPLACE FUNCTION inet_ntoa( p_ip_addr NUMBER ) RETURN VARCHAR2
    IS
        v_inet_ntoa VARCHAR(50);
    BEGIN
        v_inet_ntoa:= MOD( TRUNC(p_ip_addr/256/256/256), 256 ) || '.' ||
            MOD( TRUNC(p_ip_addr/256/256), 256 ) || '.' ||
            MOD( TRUNC(p_ip_addr/256), 256 ) || '.' ||
            MOD( p_ip_addr , 256 );
        RETURN v_inet_ntoa;
    END inet_ntoa;
");


$installer->getConnection()->query("
   create or replace function calculate_checksum(
     p_owner in varchar2,
     p_tname in varchar2,
     p_rowid in rowid )
   return  number
   is
      l_theQuery     varchar2(32000) default NULL;
      l_cursor       integer;
      l_variable     number;
      l_status       number;
      l_column_name  varchar2(255);
      p_schema       varchar2(30) := upper(p_owner);
      p_obj          varchar2(30) := upper(p_tname);
   begin
      l_cursor := dbms_sql.open_cursor;
      DBMS_SQL.parse(
               l_cursor,
               'select column_name
                  from all_tab_columns
                where owner = :p_schema
                and table_name = :p_obj
                order by column_id', dbms_sql.native);
      dbms_sql.bind_variable(l_cursor, ':p_schema', p_schema);
      dbms_sql.bind_variable(l_cursor, ':p_obj', p_obj);
      dbms_sql.define_column(l_cursor, 1, l_column_name, 255);

      l_status := dbms_sql.execute(l_cursor);
      loop
          l_status := dbms_sql.fetch_rows(l_cursor);
         if (l_status <= 0) then
             exit;
          end if;
          dbms_sql.column_value(l_cursor, 1, l_column_name);
          if (l_theQuery is NULL) then
             l_theQuery := 'select checksum_string(';
          else
             l_theQuery := l_theQuery || '||';
          end if;
          l_theQuery := l_theQuery || l_column_name;
--           dbms_output.put_line(l_theQuery);
      end loop;
      dbms_sql.close_cursor(l_cursor);

      l_theQuery := l_theQuery || ') from ' || p_schema || '.' || p_obj ||
                    ' where rowid = :x1 ';-- for update

      l_cursor := dbms_sql.open_cursor;
--       dbms_output.put_line(l_theQuery);
--       dbms_output.put_line(p_rowid);

      DBMS_SQL.parse( l_cursor, l_theQuery, dbms_sql.v7);
      dbms_sql.bind_variable( l_cursor, ':x1', p_rowid );
      dbms_sql.define_column( l_cursor, 1, l_variable );

      l_status := dbms_sql.execute(l_cursor);
      l_status := dbms_sql.fetch_rows(l_cursor);
      dbms_sql.column_value( l_cursor, 1, l_variable );
      dbms_sql.close_cursor( l_cursor );

      return l_variable;
   end;
   ");

$installer->getConnection()->query("
    create or replace function checksum(owner varchar2, table_name varchar2, row_id rowid)
    return number as
    -- pragma autonomous_transaction;
        n number;
    begin

        n := calculate_checksum(owner, table_name, row_id);
        -- n := owa_opt_lock.checksum(owner, table_name, row_id);
        -- commit;
        return n;
    end;
");


$installer->getConnection()->query("
    CREATE OR REPLACE FUNCTION FIND_IN_SET(p_what_search IN VARCHAR2, p_where_search IN VARCHAR2) RETURN NUMBER
    AS LANGUAGE JAVA
    NAME 'StringFuctions.findInSet (java.lang.String, java.lang.String)
      return java.lang.int';
");

/**
 * Create GROUP_CONCAT function
 */

$installer->getConnection()->query("
CREATE OR REPLACE TYPE typ_group_concat_expr AS OBJECT (
  string_value VARCHAR2 (4000),
  separator VARCHAR2 (4000)
);
");

$installer->getConnection()->query("
CREATE OR REPLACE TYPE typ_group_concat IS OBJECT (
  string_value VARCHAR2 (4000),
  separator VARCHAR2 (4000),

    STATIC FUNCTION ODCIAggregateInitialize (
      sctx IN OUT typ_group_concat)
      RETURN NUMBER,

    MEMBER FUNCTION ODCIAggregateIterate (
      SELF IN OUT typ_group_concat,
      ctx IN typ_group_concat_expr)
      RETURN NUMBER,

    MEMBER FUNCTION ODCIAggregateTerminate (
      SELF IN typ_group_concat,
      return_value OUT VARCHAR2,
      flags IN NUMBER)
      RETURN NUMBER,

    MEMBER FUNCTION ODCIAggregateMerge (
      SELF IN OUT typ_group_concat,
      ctx2 typ_group_concat)
      RETURN NUMBER
);
");

$installer->getConnection()->query("
CREATE OR REPLACE TYPE BODY typ_group_concat
AS
  STATIC FUNCTION ODCIAggregateInitialize (
    sctx IN OUT typ_group_concat)
    RETURN NUMBER
  IS
    BEGIN
      sctx := typ_group_concat (NULL, NULL);
      RETURN ODCIConst.Success;
  END;

  MEMBER FUNCTION ODCIAggregateIterate (
    SELF IN OUT typ_group_concat,
    ctx IN typ_group_concat_expr)
    RETURN NUMBER
  IS
  BEGIN
    IF SELF.string_value IS NOT NULL THEN
      SELF.string_value := SELF.string_value || ctx.separator;
    END IF;
    SELF.string_value := SELF.string_value || ctx.string_value;
    RETURN ODCIConst.Success;
  END;

  MEMBER FUNCTION ODCIAggregateTerminate (
    SELF IN typ_group_concat,
    return_value OUT VARCHAR2,
    flags IN NUMBER)
    RETURN NUMBER
  IS
  BEGIN
    return_value := SELF.string_value;
    RETURN ODCIConst.Success;
  END;

  MEMBER FUNCTION ODCIAggregateMerge (
    SELF IN OUT typ_group_concat,
    ctx2 IN typ_group_concat)
    RETURN NUMBER
  IS
  BEGIN
    IF SELF.string_value IS NOT NULL THEN
      SELF.string_value := SELF.string_value || SELF.separator;
    END IF;
    SELF.string_value := SELF.string_value || ctx2.string_value;
    RETURN ODCIConst.Success;
  END;
END;
");


$installer->getConnection()->query("
CREATE OR REPLACE FUNCTION group_concat (
  ctx IN typ_group_concat_expr)
  RETURN VARCHAR2
     DETERMINISTIC
    PARALLEL_ENABLE
  AGGREGATE USING typ_group_concat;
");

$installFile = dirname(__FILE__) . DS . 'install-1.6.0.0.php';
if (file_exists($installFile)) {
    include $installFile;
}
