/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
