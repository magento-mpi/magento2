/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package wsi;

import org.junit.After;
import org.junit.AfterClass;
import org.junit.Before;
import org.junit.BeforeClass;
import org.junit.Test;
import static org.junit.Assert.*;

/**
 *
 * @author taras
 */
public class CatalogTest {
    public static boolean isLog = false;
    public static int createdCategoryId;
    public static int parentCategoryId = 2;

    public CatalogTest() {
    }

    @BeforeClass
    public static void setUpClass() throws Exception {
        wsi.Main.login(isLog);
    }

    @AfterClass
    public static void tearDownClass() throws Exception {
    }

    @Before
    public void setUp() {
    }

    @After
    public void tearDown() {
    }

    /**
     * Test of create method, of class Catalog.
     */
    @Test
    public void testCreate() {
        System.out.println("create");
        int expResult = -1;
        createdCategoryId = Catalog.create(isLog, parentCategoryId);
        assertTrue(expResult < createdCategoryId);
//        // TODO review the generated test code and remove the default call to fail.
//        fail("The test case is a prototype.");
    }

    /**
     * Test of update method, of class Catalog.
     */
    @Test
    public void testUpdate() {
        System.out.println("update");
        boolean expResult = true;
        if(isLog){
            System.out.println("Trying to update category: " + createdCategoryId);
        }
        boolean result = Catalog.update(isLog, createdCategoryId);
        assertEquals(expResult, result);
//        // TODO review the generated test code and remove the default call to fail.
//        fail("The test case is a prototype.");
    }

    /**
     * Test of level method, of class Catalog.
     */
    @Test
    public void testLevel() {
        System.out.println("level");
        int expResult = 0;
        int result = Catalog.level(isLog, parentCategoryId);
        assertTrue(expResult < result);
//        // TODO review the generated test code and remove the default call to fail.
//        fail("The test case is a prototype.");
    }

    /**
     * Test of level method, of class Catalog.
     */
    @Test
    public void testTree() {
        System.out.println("tree");
        int expResult = 0;
        int result = Catalog.tree(isLog, parentCategoryId);
        assertTrue(expResult < result);
//        // TODO review the generated test code and remove the default call to fail.
//        fail("The test case is a prototype.");
    }

    /**
     * Test of level method, of class Catalog.
     */
    @Test
    public void testInfo() {
        System.out.println("info");
        String expResult = java.lang.Integer.toString(createdCategoryId);
        String result = Catalog.info(isLog, createdCategoryId);
        assertEquals(expResult, result);
//        // TODO review the generated test code and remove the default call to fail.
//        fail("The test case is a prototype.");
    }

}