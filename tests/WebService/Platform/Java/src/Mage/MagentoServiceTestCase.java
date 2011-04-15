/**
 * MagentoServiceTestCase.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis 1.4 Apr 22, 2006 (06:55:48 PDT) WSDL2Java emitter.
 */

package Mage;

public class MagentoServiceTestCase extends junit.framework.TestCase {
    public MagentoServiceTestCase(java.lang.String name) {
        super(name);
    }

    public void testMage_Api_Model_Server_V2_HandlerPortWSDL() throws Exception {
        javax.xml.rpc.ServiceFactory serviceFactory = javax.xml.rpc.ServiceFactory.newInstance();
        java.net.URL url = new java.net.URL(new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPortAddress() + "?wsdl");
        javax.xml.rpc.Service service = serviceFactory.createService(url, new Mage.MagentoServiceLocator().getServiceName());
        assertTrue(service != null);
    }

    public void test1Mage_Api_Model_Server_V2_HandlerPortEndSession() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        boolean value = false;
        value = binding.endSession(new java.lang.String());
        // TBD - validate results
        assertTrue("endSession is false", value);
    }

    public void test2Mage_Api_Model_Server_V2_HandlerPortLogin() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        try {
            binding.login(new java.lang.String(), new java.lang.String());
        } catch(org.apache.axis.AxisFault jre) {
            assertNotNull("jre is null", jre);

        }

        // Test operation
        java.lang.String value = null;
        value = binding.login(new java.lang.String(), new java.lang.String());
        // TBD - validate results
        assertNotNull( "Session is null", value );
    }

    public void test3Mage_Api_Model_Server_V2_HandlerPortStartSession() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        java.lang.String value = null;
        value = binding.startSession();
        // TBD - validate results
        assertNotNull( "Session is null", value );
    }

    public void test4Mage_Api_Model_Server_V2_HandlerPortResources() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        java.lang.String sessionId = null;
        sessionId = binding.login(new java.lang.String(), new java.lang.String());
        assertNotNull( "Session is null", sessionId );

        // Test operation
        Mage.ApiEntity[] value = null;
        value = binding.resources(sessionId);
        // TBD - validate results
        assertTrue( "length is 0", value.length > 0 );
    }

    public void test5Mage_Api_Model_Server_V2_HandlerPortGlobalFaults() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.ExistsFaltureEntity[] value = null;
        value = binding.globalFaults(new java.lang.String());
        // TBD - validate results
    }

    public void test6Mage_Api_Model_Server_V2_HandlerPortResourceFaults() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.ExistsFaltureEntity[] value = null;
        value = binding.resourceFaults(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test7Mage_Api_Model_Server_V2_HandlerPortDirectoryCountryList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.DirectoryCountryEntity[] value = null;
        value = binding.directoryCountryList(new java.lang.String());
        // TBD - validate results
    }

    public void test8Mage_Api_Model_Server_V2_HandlerPortDirectoryRegionList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.DirectoryRegionEntity[] value = null;
        value = binding.directoryRegionList(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test9Mage_Api_Model_Server_V2_HandlerPortCustomerCustomerList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CustomerCustomerEntity[] value = null;
        value = binding.customerCustomerList(new java.lang.String(), new Mage.Filters());
        // TBD - validate results
    }

    public void test10Mage_Api_Model_Server_V2_HandlerPortCustomerCustomerCreate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.customerCustomerCreate(new java.lang.String(), new Mage.CustomerCustomerEntityToCreate());
        // TBD - validate results
    }

    public void test11Mage_Api_Model_Server_V2_HandlerPortCustomerCustomerInfo() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CustomerCustomerEntity value = null;
        value = binding.customerCustomerInfo(new java.lang.String(), 0, new java.lang.String[0]);
        // TBD - validate results
    }

    public void test12Mage_Api_Model_Server_V2_HandlerPortCustomerCustomerUpdate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        boolean value = false;
        value = binding.customerCustomerUpdate(new java.lang.String(), 0, new Mage.CustomerCustomerEntityToCreate());
        // TBD - validate results
    }

    public void test13Mage_Api_Model_Server_V2_HandlerPortCustomerCustomerDelete() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        boolean value = false;
        value = binding.customerCustomerDelete(new java.lang.String(), 0);
        // TBD - validate results
    }

    public void test14Mage_Api_Model_Server_V2_HandlerPortCustomerGroupList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CustomerGroupEntity[] value = null;
        value = binding.customerGroupList(new java.lang.String());
        // TBD - validate results
    }

    public void test15Mage_Api_Model_Server_V2_HandlerPortCustomerAddressList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CustomerAddressEntityItem[] value = null;
        value = binding.customerAddressList(new java.lang.String(), 0);
        // TBD - validate results
    }

    public void test16Mage_Api_Model_Server_V2_HandlerPortCustomerAddressCreate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.customerAddressCreate(new java.lang.String(), 0, new Mage.CustomerAddressEntityCreate());
        // TBD - validate results
    }

    public void test17Mage_Api_Model_Server_V2_HandlerPortCustomerAddressInfo() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CustomerAddressEntityItem value = null;
        value = binding.customerAddressInfo(new java.lang.String(), 0);
        // TBD - validate results
    }

    public void test18Mage_Api_Model_Server_V2_HandlerPortCustomerAddressUpdate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        boolean value = false;
        value = binding.customerAddressUpdate(new java.lang.String(), 0, new Mage.CustomerAddressEntityCreate());
        // TBD - validate results
    }

    public void test19Mage_Api_Model_Server_V2_HandlerPortCustomerAddressDelete() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        boolean value = false;
        value = binding.customerAddressDelete(new java.lang.String(), 0);
        // TBD - validate results
    }

    public void test20Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryCurrentStore() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogCategoryCurrentStore(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test21Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryTree() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogCategoryTree value = null;
        value = binding.catalogCategoryTree(new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test22Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryLevel() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogCategoryEntityNoChildren[] value = null;
        value = binding.catalogCategoryLevel(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test23Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryInfo() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogCategoryInfo value = null;
        value = binding.catalogCategoryInfo(new java.lang.String(), 0, new java.lang.String(), new java.lang.String[0]);
        // TBD - validate results
    }

    public void test24Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryCreate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogCategoryCreate(new java.lang.String(), 0, new Mage.CatalogCategoryEntityCreate(), new java.lang.String());
        // TBD - validate results
    }

    public void test25Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryUpdate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        boolean value = false;
        value = binding.catalogCategoryUpdate(new java.lang.String(), 0, new Mage.CatalogCategoryEntityCreate(), new java.lang.String());
        // TBD - validate results
    }

    public void test26Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryMove() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        boolean value = false;
        value = binding.catalogCategoryMove(new java.lang.String(), 0, 0, new java.lang.String());
        // TBD - validate results
    }

    public void test27Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryDelete() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        boolean value = false;
        value = binding.catalogCategoryDelete(new java.lang.String(), 0);
        // TBD - validate results
    }

    public void test28Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryAssignedProducts() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogAssignedProduct[] value = null;
        value = binding.catalogCategoryAssignedProducts(new java.lang.String(), 0);
        // TBD - validate results
    }

    public void test29Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryAssignProduct() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        boolean value = false;
        value = binding.catalogCategoryAssignProduct(new java.lang.String(), 0, new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test30Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryUpdateProduct() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        boolean value = false;
        value = binding.catalogCategoryUpdateProduct(new java.lang.String(), 0, new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test31Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryRemoveProduct() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        boolean value = false;
        value = binding.catalogCategoryRemoveProduct(new java.lang.String(), 0, new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test32Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryAttributeCurrentStore() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogCategoryAttributeCurrentStore(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test33Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryAttributeList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogAttributeEntity[] value = null;
        value = binding.catalogCategoryAttributeList(new java.lang.String());
        // TBD - validate results
    }

    public void test34Mage_Api_Model_Server_V2_HandlerPortCatalogCategoryAttributeOptions() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogAttributeOptionEntity[] value = null;
        value = binding.catalogCategoryAttributeOptions(new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test35Mage_Api_Model_Server_V2_HandlerPortCatalogProductCurrentStore() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogProductCurrentStore(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test36Mage_Api_Model_Server_V2_HandlerPortCatalogProductList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogProductEntity[] value = null;
        value = binding.catalogProductList(new java.lang.String(), new Mage.Filters(), new java.lang.String());
        // TBD - validate results
    }

    public void test37Mage_Api_Model_Server_V2_HandlerPortCatalogProductInfo() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogProductReturnEntity value = null;
        value = binding.catalogProductInfo(new java.lang.String(), new java.lang.String(), new java.lang.String(), new Mage.CatalogProductRequestAttributes(), new java.lang.String());
        // TBD - validate results
    }

    public void test38Mage_Api_Model_Server_V2_HandlerPortCatalogProductCreate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogProductCreate(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String(), new Mage.CatalogProductCreateEntity());
        // TBD - validate results
    }

    public void test39Mage_Api_Model_Server_V2_HandlerPortCatalogProductUpdate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        boolean value = false;
        value = binding.catalogProductUpdate(new java.lang.String(), new java.lang.String(), new Mage.CatalogProductCreateEntity(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test40Mage_Api_Model_Server_V2_HandlerPortCatalogProductSetSpecialPrice() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogProductSetSpecialPrice(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test41Mage_Api_Model_Server_V2_HandlerPortCatalogProductGetSpecialPrice() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogProductReturnEntity value = null;
        value = binding.catalogProductGetSpecialPrice(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test42Mage_Api_Model_Server_V2_HandlerPortCatalogProductDelete() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogProductDelete(new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test43Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeCurrentStore() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogProductAttributeCurrentStore(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test44Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogAttributeEntity[] value = null;
        value = binding.catalogProductAttributeList(new java.lang.String(), 0);
        // TBD - validate results
    }

    public void test45Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeOptions() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogAttributeOptionEntity[] value = null;
        value = binding.catalogProductAttributeOptions(new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test46Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeSetList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogProductAttributeSetEntity[] value = null;
        value = binding.catalogProductAttributeSetList(new java.lang.String());
        // TBD - validate results
    }

    public void test47Mage_Api_Model_Server_V2_HandlerPortCatalogProductTypeList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogProductTypeEntity[] value = null;
        value = binding.catalogProductTypeList(new java.lang.String());
        // TBD - validate results
    }

    public void test48Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeTierPriceInfo() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogProductTierPriceEntity[] value = null;
        value = binding.catalogProductAttributeTierPriceInfo(new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test49Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeTierPriceUpdate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogProductAttributeTierPriceUpdate(new java.lang.String(), new java.lang.String(), new Mage.CatalogProductTierPriceEntity[0], new java.lang.String());
        // TBD - validate results
    }

    public void test50Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeMediaCurrentStore() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogProductAttributeMediaCurrentStore(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test51Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeMediaList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogProductImageEntity[] value = null;
        value = binding.catalogProductAttributeMediaList(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test52Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeMediaInfo() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogProductImageEntity value = null;
        value = binding.catalogProductAttributeMediaInfo(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test53Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeMediaTypes() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogProductAttributeMediaTypeEntity[] value = null;
        value = binding.catalogProductAttributeMediaTypes(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test54Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeMediaCreate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        java.lang.String value = null;
        value = binding.catalogProductAttributeMediaCreate(new java.lang.String(), new java.lang.String(), new Mage.CatalogProductAttributeMediaCreateEntity(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test55Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeMediaUpdate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogProductAttributeMediaUpdate(new java.lang.String(), new java.lang.String(), new java.lang.String(), new Mage.CatalogProductAttributeMediaCreateEntity(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test56Mage_Api_Model_Server_V2_HandlerPortCatalogProductAttributeMediaRemove() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogProductAttributeMediaRemove(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test57Mage_Api_Model_Server_V2_HandlerPortCatalogProductLinkList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogProductLinkEntity[] value = null;
        value = binding.catalogProductLinkList(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test58Mage_Api_Model_Server_V2_HandlerPortCatalogProductLinkAssign() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        java.lang.String value = null;
        value = binding.catalogProductLinkAssign(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String(), new Mage.CatalogProductLinkEntity(), new java.lang.String());
        // TBD - validate results
    }

    public void test59Mage_Api_Model_Server_V2_HandlerPortCatalogProductLinkUpdate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        java.lang.String value = null;
        value = binding.catalogProductLinkUpdate(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String(), new Mage.CatalogProductLinkEntity(), new java.lang.String());
        // TBD - validate results
    }

    public void test60Mage_Api_Model_Server_V2_HandlerPortCatalogProductLinkRemove() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        java.lang.String value = null;
        value = binding.catalogProductLinkRemove(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test61Mage_Api_Model_Server_V2_HandlerPortCatalogProductLinkTypes() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        java.lang.String[] value = null;
        value = binding.catalogProductLinkTypes(new java.lang.String());
        // TBD - validate results
    }

    public void test62Mage_Api_Model_Server_V2_HandlerPortCatalogProductLinkAttributes() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogProductLinkAttributeEntity[] value = null;
        value = binding.catalogProductLinkAttributes(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test63Mage_Api_Model_Server_V2_HandlerPortSalesOrderList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.SalesOrderEntity[] value = null;
        value = binding.salesOrderList(new java.lang.String(), new Mage.Filters());
        // TBD - validate results
    }

    public void test64Mage_Api_Model_Server_V2_HandlerPortSalesOrderInfo() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.SalesOrderEntity value = null;
        value = binding.salesOrderInfo(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test65Mage_Api_Model_Server_V2_HandlerPortSalesOrderAddComment() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.salesOrderAddComment(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test66Mage_Api_Model_Server_V2_HandlerPortSalesOrderHold() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.salesOrderHold(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test67Mage_Api_Model_Server_V2_HandlerPortSalesOrderUnhold() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.salesOrderUnhold(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test68Mage_Api_Model_Server_V2_HandlerPortSalesOrderCancel() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.salesOrderCancel(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test69Mage_Api_Model_Server_V2_HandlerPortSalesOrderShipmentList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.SalesOrderShipmentEntity[] value = null;
        value = binding.salesOrderShipmentList(new java.lang.String(), new Mage.Filters());
        // TBD - validate results
    }

    public void test70Mage_Api_Model_Server_V2_HandlerPortSalesOrderShipmentInfo() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.SalesOrderShipmentEntity value = null;
        value = binding.salesOrderShipmentInfo(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test71Mage_Api_Model_Server_V2_HandlerPortSalesOrderShipmentCreate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        java.lang.String value = null;
        value = binding.salesOrderShipmentCreate(new java.lang.String(), new java.lang.String(), new Mage.OrderItemIdQty[0], new java.lang.String(), 0, 0);
        // TBD - validate results
    }

    public void test72Mage_Api_Model_Server_V2_HandlerPortSalesOrderShipmentAddComment() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.salesOrderShipmentAddComment(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test73Mage_Api_Model_Server_V2_HandlerPortSalesOrderShipmentAddTrack() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.salesOrderShipmentAddTrack(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test74Mage_Api_Model_Server_V2_HandlerPortSalesOrderShipmentRemoveTrack() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.salesOrderShipmentRemoveTrack(new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test75Mage_Api_Model_Server_V2_HandlerPortSalesOrderShipmentGetCarriers() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.AssociativeEntity[] value = null;
        value = binding.salesOrderShipmentGetCarriers(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test76Mage_Api_Model_Server_V2_HandlerPortSalesOrderInvoiceList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.SalesOrderInvoiceEntity[] value = null;
        value = binding.salesOrderInvoiceList(new java.lang.String(), new Mage.Filters());
        // TBD - validate results
    }

    public void test77Mage_Api_Model_Server_V2_HandlerPortSalesOrderInvoiceInfo() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.SalesOrderInvoiceEntity value = null;
        value = binding.salesOrderInvoiceInfo(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test78Mage_Api_Model_Server_V2_HandlerPortSalesOrderInvoiceCreate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        java.lang.String value = null;
        value = binding.salesOrderInvoiceCreate(new java.lang.String(), new java.lang.String(), new Mage.OrderItemIdQty[0], new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test79Mage_Api_Model_Server_V2_HandlerPortSalesOrderInvoiceAddComment() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        java.lang.String value = null;
        value = binding.salesOrderInvoiceAddComment(new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test80Mage_Api_Model_Server_V2_HandlerPortSalesOrderInvoiceCapture() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        java.lang.String value = null;
        value = binding.salesOrderInvoiceCapture(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test81Mage_Api_Model_Server_V2_HandlerPortSalesOrderInvoiceVoid() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        java.lang.String value = null;
        value = binding.salesOrderInvoiceVoid(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test82Mage_Api_Model_Server_V2_HandlerPortSalesOrderInvoiceCancel() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        java.lang.String value = null;
        value = binding.salesOrderInvoiceCancel(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

    public void test83Mage_Api_Model_Server_V2_HandlerPortCatalogInventoryStockItemList() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        Mage.CatalogInventoryStockItemEntity[] value = null;
        value = binding.catalogInventoryStockItemList(new java.lang.String(), new java.lang.String[0]);
        // TBD - validate results
    }

    public void test84Mage_Api_Model_Server_V2_HandlerPortCatalogInventoryStockItemUpdate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.catalogInventoryStockItemUpdate(new java.lang.String(), new java.lang.String(), new Mage.CatalogInventoryStockItemUpdateEntity());
        // TBD - validate results
    }

    public void test85Mage_Api_Model_Server_V2_HandlerPortShoppingCartCreate() throws Exception {
        Mage.Mage_Api_Model_Server_V2_HandlerBindingStub binding;
        try {
            binding = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator().getMage_Api_Model_Server_V2_HandlerPort();
        }
        catch (javax.xml.rpc.ServiceException jre) {
            if(jre.getLinkedCause()!=null)
                jre.getLinkedCause().printStackTrace();
            throw new junit.framework.AssertionFailedError("JAX-RPC ServiceException caught: " + jre);
        }
        assertNotNull("binding is null", binding);

        // Time out after a minute
        binding.setTimeout(60000);

        // Test operation
        int value = -3;
        value = binding.shoppingCartCreate(new java.lang.String(), new java.lang.String());
        // TBD - validate results
    }

}
