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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

PURGE RECYCLEBIN;
/
DECLARE
   TYPE typ_object_table IS TABLE OF user_objects.object_name%TYPE;
   l_objects typ_object_table;
   l_current_script VARCHAR2(4000);
   l_is_type_exists NUMBER DEFAULT 1;
   l_try_count NUMBER DEFAULT 0;
BEGIN
  FOR cur_foreign_keys IN (
    SELECT
      'ALTER TABLE ' || table_name || ' DROP CONSTRAINT ' || constraint_name AS script
    FROM user_constraints
    WHERE constraint_type = 'R' )
  LOOP
    EXECUTE IMMEDIATE cur_foreign_keys.script;
  END LOOP;

  WHILE (l_is_type_exists > 0 AND l_try_count < 10)
  LOOP
    BEGIN
      FOR cur_types IN (
        SELECT
          'DROP ' || object_type || ' ' || object_name AS script
        FROM user_objects
        WHERE object_type  = 'TYPE' )
      LOOP
        BEGIN
          l_current_script := cur_types.script;
          EXECUTE IMMEDIATE cur_types.script;
        EXCEPTION WHEN OTHERS THEN NULL;
        END;
      END LOOP;

      SELECT COUNT(1)
      INTO l_is_type_exists
      FROM user_objects
      WHERE object_type  = 'TYPE';

      l_try_count := l_try_count + 1;
    END;
  END LOOP;

  l_objects := typ_object_table('JAVA SOURCE', 'FUNCTION', 'PROCEDURE', 'SEQUENCE', 'PACKAGE', 'TRIGGER', 'TABLE');

  FOR i IN l_objects.FIRST .. l_objects.LAST
  LOOP
    FOR cur_objects IN (
      SELECT
        'DROP ' || object_type || ' ' || object_name AS script
      FROM user_objects
      WHERE object_type  = l_objects(i)
          AND object_name NOT LIKE 'DR$FTI%'
              AND NOT EXISTS (
          SELECT 1
          FROM user_recyclebin
          WHERE user_objects.object_name =  user_recyclebin.original_name )
          )
    LOOP
      l_current_script := cur_objects.script;
      EXECUTE IMMEDIATE cur_objects.script;
    END LOOP;
  END LOOP;
/*
  EXCEPTION
    WHEN OTHERS THEN
      dbms_output.put_line(l_current_script);
      dbms_output.put_line(SQLERRM);
*/
END;
/
PURGE RECYCLEBIN;

exit;
