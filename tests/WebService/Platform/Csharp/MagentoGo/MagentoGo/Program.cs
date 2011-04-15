using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace MagentoGo
{
    class Program
    {
        static void Main(string[] args)
        {
            String apiUser = System.Configuration.ConfigurationSettings.AppSettings["apiUser"];
            String apiKey  = System.Configuration.ConfigurationSettings.AppSettings["apiKey"];
            try
            {
                Mage.Mage_Api_Model_Server_V2_HandlerPortTypeClient proxy = new Mage.Mage_Api_Model_Server_V2_HandlerPortTypeClient();
                String sessionId = proxy.login(apiUser, apiKey);

                System.Console.WriteLine("SessionId: " + sessionId);


            }
            catch (Exception e)
            {
                System.Console.WriteLine("Error Message: " + e.Message);
                System.Console.WriteLine("StackTrace: "    + e.StackTrace);
            }
        }
    }
}
