package example;

public class MagentoGoClient {
    public static final java.lang.String serverAddress = "http://your_magento_host.gostorego.com/index.php/api/v2_soap/index/";
    public static final java.lang.String ApiUser = "WebApiUser";
    public static final java.lang.String ApiKey = "WebApiKey";

    public static void main(String[] argv) {
        try {
            Mage.Mage_Api_Model_Server_V2_HandlerBindingStub proxy = (Mage.Mage_Api_Model_Server_V2_HandlerBindingStub)
                          new Mage.MagentoServiceLocator()
                                  .setMage_Api_Model_Server_V2_HandlerPortAddress(MagentoGoClient.serverAddress)
                                  .getMage_Api_Model_Server_V2_HandlerPort();
            java.lang.String sessionId = proxy.login(MagentoGoClient.ApiUser, MagentoGoClient.ApiKey);
            System.out.println( "SessionId: " + sessionId );
        }
        catch(javax.xml.rpc.ServiceException ex) { ex.printStackTrace(); }
        catch(java.lang.Exception ex) { ex.printStackTrace(); }
  }
}