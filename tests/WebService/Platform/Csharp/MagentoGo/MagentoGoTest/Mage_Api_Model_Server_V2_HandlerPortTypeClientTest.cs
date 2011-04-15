using MagentoGo.Mage;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using System;
using System.ServiceModel;
using System.ServiceModel.Channels;

namespace MagentoGoTest
{
    
    
    /// <summary>
    ///This is a test class for Mage_Api_Model_Server_V2_HandlerPortTypeClientTest and is intended
    ///to contain all Mage_Api_Model_Server_V2_HandlerPortTypeClientTest Unit Tests
    ///</summary>
    [TestClass()]
    public class Mage_Api_Model_Server_V2_HandlerPortTypeClientTest
    {


        private TestContext testContextInstance;

        /// <summary>
        ///Gets or sets the test context which provides
        ///information about and functionality for the current test run.
        ///</summary>
        public TestContext TestContext
        {
            get
            {
                return testContextInstance;
            }
            set
            {
                testContextInstance = value;
            }
        }

        #region Additional test attributes
        // 
        //You can use the following additional attributes as you write your tests:
        //
        //Use ClassInitialize to run code before running the first test in the class
        //[ClassInitialize()]
        //public static void MyClassInitialize(TestContext testContext)
        //{
        //}
        //
        //Use ClassCleanup to run code after all tests in a class have run
        //[ClassCleanup()]
        //public static void MyClassCleanup()
        //{
        //}
        //
        //Use TestInitialize to run code before running each test
        //[TestInitialize()]
        //public void MyTestInitialize()
        //{
        //}
        //
        //Use TestCleanup to run code after each test has run
        //[TestCleanup()]
        //public void MyTestCleanup()
        //{
        //}
        //
        #endregion


        /// <summary>
        ///A test for Mage_Api_Model_Server_V2_HandlerPortTypeClient Constructor
        ///</summary>
        [TestMethod()]
        public void Mage_Api_Model_Server_V2_HandlerPortTypeClientConstructorTest()
        {
            string endpointConfigurationName = string.Empty; // TODO: Initialize to an appropriate value
            EndpointAddress remoteAddress = null; // TODO: Initialize to an appropriate value
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(endpointConfigurationName, remoteAddress);
            Assert.Inconclusive("TODO: Implement code to verify target");
        }

        /// <summary>
        ///A test for Mage_Api_Model_Server_V2_HandlerPortTypeClient Constructor
        ///</summary>
        [TestMethod()]
        public void Mage_Api_Model_Server_V2_HandlerPortTypeClientConstructorTest1()
        {
            Binding binding = null; // TODO: Initialize to an appropriate value
            EndpointAddress remoteAddress = null; // TODO: Initialize to an appropriate value
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(binding, remoteAddress);
            Assert.Inconclusive("TODO: Implement code to verify target");
        }

        /// <summary>
        ///A test for Mage_Api_Model_Server_V2_HandlerPortTypeClient Constructor
        ///</summary>
        [TestMethod()]
        public void Mage_Api_Model_Server_V2_HandlerPortTypeClientConstructorTest2()
        {
            string endpointConfigurationName = string.Empty; // TODO: Initialize to an appropriate value
            string remoteAddress = string.Empty; // TODO: Initialize to an appropriate value
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(endpointConfigurationName, remoteAddress);
            Assert.Inconclusive("TODO: Implement code to verify target");
        }

        /// <summary>
        ///A test for Mage_Api_Model_Server_V2_HandlerPortTypeClient Constructor
        ///</summary>
        [TestMethod()]
        public void Mage_Api_Model_Server_V2_HandlerPortTypeClientConstructorTest3()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient();
            Assert.Inconclusive("TODO: Implement code to verify target");
        }

        /// <summary>
        ///A test for Mage_Api_Model_Server_V2_HandlerPortTypeClient Constructor
        ///</summary>
        [TestMethod()]
        public void Mage_Api_Model_Server_V2_HandlerPortTypeClientConstructorTest4()
        {
            string endpointConfigurationName = string.Empty; // TODO: Initialize to an appropriate value
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(endpointConfigurationName);
            Assert.Inconclusive("TODO: Implement code to verify target");
        }

        /// <summary>
        ///A test for catalogCategoryAssignProduct
        ///</summary>
        [TestMethod()]
        public void catalogCategoryAssignProductTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int categoryId = 0; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string position = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            bool expected = false; // TODO: Initialize to an appropriate value
            bool actual;
            actual = target.catalogCategoryAssignProduct(sessionId, categoryId, product, position, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogCategoryAssignedProducts
        ///</summary>
        [TestMethod()]
        public void catalogCategoryAssignedProductsTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int categoryId = 0; // TODO: Initialize to an appropriate value
            catalogAssignedProduct[] expected = null; // TODO: Initialize to an appropriate value
            catalogAssignedProduct[] actual;
            actual = target.catalogCategoryAssignedProducts(sessionId, categoryId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogCategoryAttributeCurrentStore
        ///</summary>
        [TestMethod()]
        public void catalogCategoryAttributeCurrentStoreTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            int storeView1 = 0; // TODO: Initialize to an appropriate value
            int storeView1Expected = 0; // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            target.catalogCategoryAttributeCurrentStore(out storeView1, sessionId, storeView);
            Assert.AreEqual(storeView1Expected, storeView1);
            Assert.Inconclusive("A method that does not return a value cannot be verified.");
        }

        /// <summary>
        ///A test for catalogCategoryAttributeList
        ///</summary>
        [TestMethod()]
        public void catalogCategoryAttributeListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            catalogAttributeEntity[] expected = null; // TODO: Initialize to an appropriate value
            catalogAttributeEntity[] actual;
            actual = target.catalogCategoryAttributeList(sessionId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogCategoryAttributeOptions
        ///</summary>
        [TestMethod()]
        public void catalogCategoryAttributeOptionsTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string attributeId = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            catalogAttributeOptionEntity[] expected = null; // TODO: Initialize to an appropriate value
            catalogAttributeOptionEntity[] actual;
            actual = target.catalogCategoryAttributeOptions(sessionId, attributeId, storeView);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogCategoryCreate
        ///</summary>
        [TestMethod()]
        public void catalogCategoryCreateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int parentId = 0; // TODO: Initialize to an appropriate value
            catalogCategoryEntityCreate categoryData = null; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.catalogCategoryCreate(sessionId, parentId, categoryData, storeView);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogCategoryCurrentStore
        ///</summary>
        [TestMethod()]
        public void catalogCategoryCurrentStoreTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            int storeView1 = 0; // TODO: Initialize to an appropriate value
            int storeView1Expected = 0; // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            target.catalogCategoryCurrentStore(out storeView1, sessionId, storeView);
            Assert.AreEqual(storeView1Expected, storeView1);
            Assert.Inconclusive("A method that does not return a value cannot be verified.");
        }

        /// <summary>
        ///A test for catalogCategoryDelete
        ///</summary>
        [TestMethod()]
        public void catalogCategoryDeleteTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int categoryId = 0; // TODO: Initialize to an appropriate value
            bool expected = false; // TODO: Initialize to an appropriate value
            bool actual;
            actual = target.catalogCategoryDelete(sessionId, categoryId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogCategoryInfo
        ///</summary>
        [TestMethod()]
        public void catalogCategoryInfoTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int categoryId = 0; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            string[] attributes = null; // TODO: Initialize to an appropriate value
            catalogCategoryInfo expected = null; // TODO: Initialize to an appropriate value
            catalogCategoryInfo actual;
            actual = target.catalogCategoryInfo(sessionId, categoryId, storeView, attributes);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogCategoryLevel
        ///</summary>
        [TestMethod()]
        public void catalogCategoryLevelTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string website = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            string parentCategory = string.Empty; // TODO: Initialize to an appropriate value
            catalogCategoryEntityNoChildren[] expected = null; // TODO: Initialize to an appropriate value
            catalogCategoryEntityNoChildren[] actual;
            actual = target.catalogCategoryLevel(sessionId, website, storeView, parentCategory);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogCategoryMove
        ///</summary>
        [TestMethod()]
        public void catalogCategoryMoveTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int categoryId = 0; // TODO: Initialize to an appropriate value
            int parentId = 0; // TODO: Initialize to an appropriate value
            string afterId = string.Empty; // TODO: Initialize to an appropriate value
            bool expected = false; // TODO: Initialize to an appropriate value
            bool actual;
            actual = target.catalogCategoryMove(sessionId, categoryId, parentId, afterId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogCategoryRemoveProduct
        ///</summary>
        [TestMethod()]
        public void catalogCategoryRemoveProductTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int categoryId = 0; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            bool expected = false; // TODO: Initialize to an appropriate value
            bool actual;
            actual = target.catalogCategoryRemoveProduct(sessionId, categoryId, product, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogCategoryTree
        ///</summary>
        [TestMethod()]
        public void catalogCategoryTreeTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string parentId = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            catalogCategoryTree expected = null; // TODO: Initialize to an appropriate value
            catalogCategoryTree actual;
            actual = target.catalogCategoryTree(sessionId, parentId, storeView);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogCategoryUpdate
        ///</summary>
        [TestMethod()]
        public void catalogCategoryUpdateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int categoryId = 0; // TODO: Initialize to an appropriate value
            catalogCategoryEntityCreate categoryData = null; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            bool expected = false; // TODO: Initialize to an appropriate value
            bool actual;
            actual = target.catalogCategoryUpdate(sessionId, categoryId, categoryData, storeView);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogCategoryUpdateProduct
        ///</summary>
        [TestMethod()]
        public void catalogCategoryUpdateProductTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int categoryId = 0; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string position = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            bool expected = false; // TODO: Initialize to an appropriate value
            bool actual;
            actual = target.catalogCategoryUpdateProduct(sessionId, categoryId, product, position, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogInventoryStockItemList
        ///</summary>
        [TestMethod()]
        public void catalogInventoryStockItemListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string[] products = null; // TODO: Initialize to an appropriate value
            catalogInventoryStockItemEntity[] expected = null; // TODO: Initialize to an appropriate value
            catalogInventoryStockItemEntity[] actual;
            actual = target.catalogInventoryStockItemList(sessionId, products);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogInventoryStockItemUpdate
        ///</summary>
        [TestMethod()]
        public void catalogInventoryStockItemUpdateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            catalogInventoryStockItemUpdateEntity data = null; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.catalogInventoryStockItemUpdate(sessionId, product, data);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductAttributeCurrentStore
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeCurrentStoreTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            int storeView1 = 0; // TODO: Initialize to an appropriate value
            int storeView1Expected = 0; // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            target.catalogProductAttributeCurrentStore(out storeView1, sessionId, storeView);
            Assert.AreEqual(storeView1Expected, storeView1);
            Assert.Inconclusive("A method that does not return a value cannot be verified.");
        }

        /// <summary>
        ///A test for catalogProductAttributeList
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int setId = 0; // TODO: Initialize to an appropriate value
            catalogAttributeEntity[] expected = null; // TODO: Initialize to an appropriate value
            catalogAttributeEntity[] actual;
            actual = target.catalogProductAttributeList(sessionId, setId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductAttributeMediaCreate
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeMediaCreateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductAttributeMediaCreateEntity data = null; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            string expected = string.Empty; // TODO: Initialize to an appropriate value
            string actual;
            actual = target.catalogProductAttributeMediaCreate(sessionId, product, data, storeView, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductAttributeMediaCurrentStore
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeMediaCurrentStoreTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            int storeView1 = 0; // TODO: Initialize to an appropriate value
            int storeView1Expected = 0; // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            target.catalogProductAttributeMediaCurrentStore(out storeView1, sessionId, storeView);
            Assert.AreEqual(storeView1Expected, storeView1);
            Assert.Inconclusive("A method that does not return a value cannot be verified.");
        }

        /// <summary>
        ///A test for catalogProductAttributeMediaInfo
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeMediaInfoTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string file = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductImageEntity expected = null; // TODO: Initialize to an appropriate value
            catalogProductImageEntity actual;
            actual = target.catalogProductAttributeMediaInfo(sessionId, product, file, storeView, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductAttributeMediaList
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeMediaListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductImageEntity[] expected = null; // TODO: Initialize to an appropriate value
            catalogProductImageEntity[] actual;
            actual = target.catalogProductAttributeMediaList(sessionId, product, storeView, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductAttributeMediaRemove
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeMediaRemoveTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string file = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.catalogProductAttributeMediaRemove(sessionId, product, file, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductAttributeMediaTypes
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeMediaTypesTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string setId = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductAttributeMediaTypeEntity[] expected = null; // TODO: Initialize to an appropriate value
            catalogProductAttributeMediaTypeEntity[] actual;
            actual = target.catalogProductAttributeMediaTypes(sessionId, setId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductAttributeMediaUpdate
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeMediaUpdateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string file = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductAttributeMediaCreateEntity data = null; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.catalogProductAttributeMediaUpdate(sessionId, product, file, data, storeView, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductAttributeOptions
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeOptionsTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string attributeId = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            catalogAttributeOptionEntity[] expected = null; // TODO: Initialize to an appropriate value
            catalogAttributeOptionEntity[] actual;
            actual = target.catalogProductAttributeOptions(sessionId, attributeId, storeView);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductAttributeSetList
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeSetListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductAttributeSetEntity[] expected = null; // TODO: Initialize to an appropriate value
            catalogProductAttributeSetEntity[] actual;
            actual = target.catalogProductAttributeSetList(sessionId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductAttributeTierPriceInfo
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeTierPriceInfoTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductTierPriceEntity[] expected = null; // TODO: Initialize to an appropriate value
            catalogProductTierPriceEntity[] actual;
            actual = target.catalogProductAttributeTierPriceInfo(sessionId, product, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductAttributeTierPriceUpdate
        ///</summary>
        [TestMethod()]
        public void catalogProductAttributeTierPriceUpdateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductTierPriceEntity[] tier_price = null; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.catalogProductAttributeTierPriceUpdate(sessionId, product, tier_price, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductCreate
        ///</summary>
        [TestMethod()]
        public void catalogProductCreateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string type = string.Empty; // TODO: Initialize to an appropriate value
            string set = string.Empty; // TODO: Initialize to an appropriate value
            string sku = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductCreateEntity productData = null; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.catalogProductCreate(sessionId, type, set, sku, productData);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductCurrentStore
        ///</summary>
        [TestMethod()]
        public void catalogProductCurrentStoreTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            int storeView1 = 0; // TODO: Initialize to an appropriate value
            int storeView1Expected = 0; // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            target.catalogProductCurrentStore(out storeView1, sessionId, storeView);
            Assert.AreEqual(storeView1Expected, storeView1);
            Assert.Inconclusive("A method that does not return a value cannot be verified.");
        }

        /// <summary>
        ///A test for catalogProductDelete
        ///</summary>
        [TestMethod()]
        public void catalogProductDeleteTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.catalogProductDelete(sessionId, product, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductGetSpecialPrice
        ///</summary>
        [TestMethod()]
        public void catalogProductGetSpecialPriceTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductReturnEntity expected = null; // TODO: Initialize to an appropriate value
            catalogProductReturnEntity actual;
            actual = target.catalogProductGetSpecialPrice(sessionId, product, storeView, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductInfo
        ///</summary>
        [TestMethod()]
        public void catalogProductInfoTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductRequestAttributes attributes = null; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductReturnEntity expected = null; // TODO: Initialize to an appropriate value
            catalogProductReturnEntity actual;
            actual = target.catalogProductInfo(sessionId, product, storeView, attributes, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductLinkAssign
        ///</summary>
        [TestMethod()]
        public void catalogProductLinkAssignTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string type = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string linkedProduct = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductLinkEntity data = null; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            string expected = string.Empty; // TODO: Initialize to an appropriate value
            string actual;
            actual = target.catalogProductLinkAssign(sessionId, type, product, linkedProduct, data, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductLinkAttributes
        ///</summary>
        [TestMethod()]
        public void catalogProductLinkAttributesTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string type = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductLinkAttributeEntity[] expected = null; // TODO: Initialize to an appropriate value
            catalogProductLinkAttributeEntity[] actual;
            actual = target.catalogProductLinkAttributes(sessionId, type);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductLinkList
        ///</summary>
        [TestMethod()]
        public void catalogProductLinkListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string type = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductLinkEntity[] expected = null; // TODO: Initialize to an appropriate value
            catalogProductLinkEntity[] actual;
            actual = target.catalogProductLinkList(sessionId, type, product, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductLinkRemove
        ///</summary>
        [TestMethod()]
        public void catalogProductLinkRemoveTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string type = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string linkedProduct = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            string expected = string.Empty; // TODO: Initialize to an appropriate value
            string actual;
            actual = target.catalogProductLinkRemove(sessionId, type, product, linkedProduct, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductLinkTypes
        ///</summary>
        [TestMethod()]
        public void catalogProductLinkTypesTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string[] expected = null; // TODO: Initialize to an appropriate value
            string[] actual;
            actual = target.catalogProductLinkTypes(sessionId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductLinkUpdate
        ///</summary>
        [TestMethod()]
        public void catalogProductLinkUpdateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string type = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string linkedProduct = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductLinkEntity data = null; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            string expected = string.Empty; // TODO: Initialize to an appropriate value
            string actual;
            actual = target.catalogProductLinkUpdate(sessionId, type, product, linkedProduct, data, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductList
        ///</summary>
        [TestMethod()]
        public void catalogProductListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            catalogProductEntity[] storeView1 = null; // TODO: Initialize to an appropriate value
            catalogProductEntity[] storeView1Expected = null; // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            filters filters = null; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            target.catalogProductList(out storeView1, sessionId, filters, storeView);
            Assert.AreEqual(storeView1Expected, storeView1);
            Assert.Inconclusive("A method that does not return a value cannot be verified.");
        }

        /// <summary>
        ///A test for catalogProductSetSpecialPrice
        ///</summary>
        [TestMethod()]
        public void catalogProductSetSpecialPriceTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            string specialPrice = string.Empty; // TODO: Initialize to an appropriate value
            string fromDate = string.Empty; // TODO: Initialize to an appropriate value
            string toDate = string.Empty; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.catalogProductSetSpecialPrice(sessionId, product, specialPrice, fromDate, toDate, storeView, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductTypeList
        ///</summary>
        [TestMethod()]
        public void catalogProductTypeListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductTypeEntity[] expected = null; // TODO: Initialize to an appropriate value
            catalogProductTypeEntity[] actual;
            actual = target.catalogProductTypeList(sessionId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for catalogProductUpdate
        ///</summary>
        [TestMethod()]
        public void catalogProductUpdateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string product = string.Empty; // TODO: Initialize to an appropriate value
            catalogProductCreateEntity productData = null; // TODO: Initialize to an appropriate value
            string storeView = string.Empty; // TODO: Initialize to an appropriate value
            string productIdentifierType = string.Empty; // TODO: Initialize to an appropriate value
            bool expected = false; // TODO: Initialize to an appropriate value
            bool actual;
            actual = target.catalogProductUpdate(sessionId, product, productData, storeView, productIdentifierType);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for customerAddressCreate
        ///</summary>
        [TestMethod()]
        public void customerAddressCreateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int customerId = 0; // TODO: Initialize to an appropriate value
            customerAddressEntityCreate addressData = null; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.customerAddressCreate(sessionId, customerId, addressData);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for customerAddressDelete
        ///</summary>
        [TestMethod()]
        public void customerAddressDeleteTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int addressId = 0; // TODO: Initialize to an appropriate value
            bool expected = false; // TODO: Initialize to an appropriate value
            bool actual;
            actual = target.customerAddressDelete(sessionId, addressId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for customerAddressInfo
        ///</summary>
        [TestMethod()]
        public void customerAddressInfoTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int addressId = 0; // TODO: Initialize to an appropriate value
            customerAddressEntityItem expected = null; // TODO: Initialize to an appropriate value
            customerAddressEntityItem actual;
            actual = target.customerAddressInfo(sessionId, addressId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for customerAddressList
        ///</summary>
        [TestMethod()]
        public void customerAddressListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int customerId = 0; // TODO: Initialize to an appropriate value
            customerAddressEntityItem[] expected = null; // TODO: Initialize to an appropriate value
            customerAddressEntityItem[] actual;
            actual = target.customerAddressList(sessionId, customerId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for customerAddressUpdate
        ///</summary>
        [TestMethod()]
        public void customerAddressUpdateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int addressId = 0; // TODO: Initialize to an appropriate value
            customerAddressEntityCreate addressData = null; // TODO: Initialize to an appropriate value
            bool expected = false; // TODO: Initialize to an appropriate value
            bool actual;
            actual = target.customerAddressUpdate(sessionId, addressId, addressData);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for customerCustomerCreate
        ///</summary>
        [TestMethod()]
        public void customerCustomerCreateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            customerCustomerEntityToCreate customerData = null; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.customerCustomerCreate(sessionId, customerData);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for customerCustomerDelete
        ///</summary>
        [TestMethod()]
        public void customerCustomerDeleteTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int customerId = 0; // TODO: Initialize to an appropriate value
            bool expected = false; // TODO: Initialize to an appropriate value
            bool actual;
            actual = target.customerCustomerDelete(sessionId, customerId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for customerCustomerInfo
        ///</summary>
        [TestMethod()]
        public void customerCustomerInfoTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int customerId = 0; // TODO: Initialize to an appropriate value
            string[] attributes = null; // TODO: Initialize to an appropriate value
            customerCustomerEntity expected = null; // TODO: Initialize to an appropriate value
            customerCustomerEntity actual;
            actual = target.customerCustomerInfo(sessionId, customerId, attributes);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for customerCustomerList
        ///</summary>
        [TestMethod()]
        public void customerCustomerListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            filters filters = null; // TODO: Initialize to an appropriate value
            customerCustomerEntity[] expected = null; // TODO: Initialize to an appropriate value
            customerCustomerEntity[] actual;
            actual = target.customerCustomerList(sessionId, filters);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for customerCustomerUpdate
        ///</summary>
        [TestMethod()]
        public void customerCustomerUpdateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            int customerId = 0; // TODO: Initialize to an appropriate value
            customerCustomerEntityToCreate customerData = null; // TODO: Initialize to an appropriate value
            bool expected = false; // TODO: Initialize to an appropriate value
            bool actual;
            actual = target.customerCustomerUpdate(sessionId, customerId, customerData);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for customerGroupList
        ///</summary>
        [TestMethod()]
        public void customerGroupListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            customerGroupEntity[] expected = null; // TODO: Initialize to an appropriate value
            customerGroupEntity[] actual;
            actual = target.customerGroupList(sessionId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for directoryCountryList
        ///</summary>
        [TestMethod()]
        public void directoryCountryListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            directoryCountryEntity[] expected = null; // TODO: Initialize to an appropriate value
            directoryCountryEntity[] actual;
            actual = target.directoryCountryList(sessionId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for directoryRegionList
        ///</summary>
        [TestMethod()]
        public void directoryRegionListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string country = string.Empty; // TODO: Initialize to an appropriate value
            directoryRegionEntity[] expected = null; // TODO: Initialize to an appropriate value
            directoryRegionEntity[] actual;
            actual = target.directoryRegionList(sessionId, country);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for endSession
        ///</summary>
        [TestMethod()]
        public void endSessionTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            bool expected = false; // TODO: Initialize to an appropriate value
            bool actual;
            actual = target.endSession(sessionId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for globalFaults
        ///</summary>
        [TestMethod()]
        public void globalFaultsTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            existsFaltureEntity[] expected = null; // TODO: Initialize to an appropriate value
            existsFaltureEntity[] actual;
            actual = target.globalFaults(sessionId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for login
        ///</summary>
        [TestMethod()]
        public void loginTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string username = string.Empty; // TODO: Initialize to an appropriate value
            string apiKey = string.Empty; // TODO: Initialize to an appropriate value
            string expected = string.Empty; // TODO: Initialize to an appropriate value
            string actual;
            actual = target.login(username, apiKey);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for resourceFaults
        ///</summary>
        [TestMethod()]
        public void resourceFaultsTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string resourceName = string.Empty; // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            existsFaltureEntity[] expected = null; // TODO: Initialize to an appropriate value
            existsFaltureEntity[] actual;
            actual = target.resourceFaults(resourceName, sessionId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for resources
        ///</summary>
        [TestMethod()]
        public void resourcesTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            apiEntity[] expected = null; // TODO: Initialize to an appropriate value
            apiEntity[] actual;
            actual = target.resources(sessionId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderAddComment
        ///</summary>
        [TestMethod()]
        public void salesOrderAddCommentTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string orderIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            string status = string.Empty; // TODO: Initialize to an appropriate value
            string comment = string.Empty; // TODO: Initialize to an appropriate value
            string notify = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.salesOrderAddComment(sessionId, orderIncrementId, status, comment, notify);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderCancel
        ///</summary>
        [TestMethod()]
        public void salesOrderCancelTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string orderIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.salesOrderCancel(sessionId, orderIncrementId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderHold
        ///</summary>
        [TestMethod()]
        public void salesOrderHoldTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string orderIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.salesOrderHold(sessionId, orderIncrementId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderInfo
        ///</summary>
        [TestMethod()]
        public void salesOrderInfoTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string orderIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            salesOrderEntity expected = null; // TODO: Initialize to an appropriate value
            salesOrderEntity actual;
            actual = target.salesOrderInfo(sessionId, orderIncrementId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderInvoiceAddComment
        ///</summary>
        [TestMethod()]
        public void salesOrderInvoiceAddCommentTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string invoiceIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            string comment = string.Empty; // TODO: Initialize to an appropriate value
            string email = string.Empty; // TODO: Initialize to an appropriate value
            string includeComment = string.Empty; // TODO: Initialize to an appropriate value
            string expected = string.Empty; // TODO: Initialize to an appropriate value
            string actual;
            actual = target.salesOrderInvoiceAddComment(sessionId, invoiceIncrementId, comment, email, includeComment);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderInvoiceCancel
        ///</summary>
        [TestMethod()]
        public void salesOrderInvoiceCancelTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string invoiceIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            string expected = string.Empty; // TODO: Initialize to an appropriate value
            string actual;
            actual = target.salesOrderInvoiceCancel(sessionId, invoiceIncrementId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderInvoiceCapture
        ///</summary>
        [TestMethod()]
        public void salesOrderInvoiceCaptureTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string invoiceIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            string expected = string.Empty; // TODO: Initialize to an appropriate value
            string actual;
            actual = target.salesOrderInvoiceCapture(sessionId, invoiceIncrementId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderInvoiceCreate
        ///</summary>
        [TestMethod()]
        public void salesOrderInvoiceCreateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string invoiceIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            orderItemIdQty[] itemsQty = null; // TODO: Initialize to an appropriate value
            string comment = string.Empty; // TODO: Initialize to an appropriate value
            string email = string.Empty; // TODO: Initialize to an appropriate value
            string includeComment = string.Empty; // TODO: Initialize to an appropriate value
            string expected = string.Empty; // TODO: Initialize to an appropriate value
            string actual;
            actual = target.salesOrderInvoiceCreate(sessionId, invoiceIncrementId, itemsQty, comment, email, includeComment);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderInvoiceInfo
        ///</summary>
        [TestMethod()]
        public void salesOrderInvoiceInfoTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string invoiceIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            salesOrderInvoiceEntity expected = null; // TODO: Initialize to an appropriate value
            salesOrderInvoiceEntity actual;
            actual = target.salesOrderInvoiceInfo(sessionId, invoiceIncrementId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderInvoiceList
        ///</summary>
        [TestMethod()]
        public void salesOrderInvoiceListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            filters filters = null; // TODO: Initialize to an appropriate value
            salesOrderInvoiceEntity[] expected = null; // TODO: Initialize to an appropriate value
            salesOrderInvoiceEntity[] actual;
            actual = target.salesOrderInvoiceList(sessionId, filters);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderInvoiceVoid
        ///</summary>
        [TestMethod()]
        public void salesOrderInvoiceVoidTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string invoiceIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            string expected = string.Empty; // TODO: Initialize to an appropriate value
            string actual;
            actual = target.salesOrderInvoiceVoid(sessionId, invoiceIncrementId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderList
        ///</summary>
        [TestMethod()]
        public void salesOrderListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            filters filters = null; // TODO: Initialize to an appropriate value
            salesOrderEntity[] expected = null; // TODO: Initialize to an appropriate value
            salesOrderEntity[] actual;
            actual = target.salesOrderList(sessionId, filters);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderShipmentAddComment
        ///</summary>
        [TestMethod()]
        public void salesOrderShipmentAddCommentTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            int shipmentIncrementId1 = 0; // TODO: Initialize to an appropriate value
            int shipmentIncrementId1Expected = 0; // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string shipmentIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            string comment = string.Empty; // TODO: Initialize to an appropriate value
            string email = string.Empty; // TODO: Initialize to an appropriate value
            string includeInEmail = string.Empty; // TODO: Initialize to an appropriate value
            target.salesOrderShipmentAddComment(out shipmentIncrementId1, sessionId, shipmentIncrementId, comment, email, includeInEmail);
            Assert.AreEqual(shipmentIncrementId1Expected, shipmentIncrementId1);
            Assert.Inconclusive("A method that does not return a value cannot be verified.");
        }

        /// <summary>
        ///A test for salesOrderShipmentAddTrack
        ///</summary>
        [TestMethod()]
        public void salesOrderShipmentAddTrackTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string shipmentIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            string carrier = string.Empty; // TODO: Initialize to an appropriate value
            string title = string.Empty; // TODO: Initialize to an appropriate value
            string trackNumber = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.salesOrderShipmentAddTrack(sessionId, shipmentIncrementId, carrier, title, trackNumber);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderShipmentCreate
        ///</summary>
        [TestMethod()]
        public void salesOrderShipmentCreateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string orderIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            orderItemIdQty[] itemsQty = null; // TODO: Initialize to an appropriate value
            string comment = string.Empty; // TODO: Initialize to an appropriate value
            int email = 0; // TODO: Initialize to an appropriate value
            int includeComment = 0; // TODO: Initialize to an appropriate value
            string expected = string.Empty; // TODO: Initialize to an appropriate value
            string actual;
            actual = target.salesOrderShipmentCreate(sessionId, orderIncrementId, itemsQty, comment, email, includeComment);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderShipmentGetCarriers
        ///</summary>
        [TestMethod()]
        public void salesOrderShipmentGetCarriersTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string orderIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            associativeEntity[] expected = null; // TODO: Initialize to an appropriate value
            associativeEntity[] actual;
            actual = target.salesOrderShipmentGetCarriers(sessionId, orderIncrementId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderShipmentInfo
        ///</summary>
        [TestMethod()]
        public void salesOrderShipmentInfoTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string shipmentIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            salesOrderShipmentEntity expected = null; // TODO: Initialize to an appropriate value
            salesOrderShipmentEntity actual;
            actual = target.salesOrderShipmentInfo(sessionId, shipmentIncrementId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderShipmentList
        ///</summary>
        [TestMethod()]
        public void salesOrderShipmentListTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            filters filters = null; // TODO: Initialize to an appropriate value
            salesOrderShipmentEntity[] expected = null; // TODO: Initialize to an appropriate value
            salesOrderShipmentEntity[] actual;
            actual = target.salesOrderShipmentList(sessionId, filters);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderShipmentRemoveTrack
        ///</summary>
        [TestMethod()]
        public void salesOrderShipmentRemoveTrackTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string shipmentIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            string trackId = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.salesOrderShipmentRemoveTrack(sessionId, shipmentIncrementId, trackId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for salesOrderUnhold
        ///</summary>
        [TestMethod()]
        public void salesOrderUnholdTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string orderIncrementId = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.salesOrderUnhold(sessionId, orderIncrementId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for shoppingCartCreate
        ///</summary>
        [TestMethod()]
        public void shoppingCartCreateTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string sessionId = string.Empty; // TODO: Initialize to an appropriate value
            string storeId = string.Empty; // TODO: Initialize to an appropriate value
            int expected = 0; // TODO: Initialize to an appropriate value
            int actual;
            actual = target.shoppingCartCreate(sessionId, storeId);
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }

        /// <summary>
        ///A test for startSession
        ///</summary>
        [TestMethod()]
        public void startSessionTest()
        {
            Mage_Api_Model_Server_V2_HandlerPortTypeClient target = new Mage_Api_Model_Server_V2_HandlerPortTypeClient(); // TODO: Initialize to an appropriate value
            string expected = string.Empty; // TODO: Initialize to an appropriate value
            string actual;
            actual = target.startSession();
            Assert.AreEqual(expected, actual);
            Assert.Inconclusive("Verify the correctness of this test method.");
        }
    }
}
