/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package wsi;

import MWSAPI.*;


/**
 *
 * @author taras
 */
public class Main {

    public static String sessionId;
    public static MWSAPI.MageApiModelServerWsiHandlerPortType proxy;

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        login(true);

        Catalog catalog = new Catalog(true);
    }

    public static void login(boolean isLog){
        MWSAPI.MagentoService ms = new MWSAPI.MagentoService();

        proxy = ms.getMageApiModelServerWsiHandlerPort();

        LoginParam lp = new LoginParam();
        lp.setApiKey("zasaqwq12");
        lp.setUsername("root");

        LoginResponseParam lr = proxy.login(lp);
        
        sessionId = lr.getResult();
        if(isLog){
            System.out.println("__________________________________________________");
            System.out.println("SessionID:");
            System.out.println(sessionId);
            System.out.println("__________________________________________________");
        }
    }

}
