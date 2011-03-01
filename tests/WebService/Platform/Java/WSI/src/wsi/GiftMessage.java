/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package wsi;

import wsi.Main.*;
import MWSAPI.*;

/**
 *
 * @author taras
 */
public class GiftMessage {

    GiftMessage(boolean isLog){
        String quoteId = "4";
        String quoteItemId = "1";
        String quoteProductId = "17";

//        quote(isLog, quoteId);
//        quoteItem(isLog, quoteItemId);
        quoteProduct(isLog, quoteProductId);
    }

    public static boolean quote(boolean isLog, String quoteId){

        GiftMessageEntity gme = new GiftMessageEntity();
        gme.setFrom("Java");
        gme.setTo("PHP");
        gme.setMessage("Message");

        GiftMessageForQuoteRequestParam in = new GiftMessageForQuoteRequestParam();
        in.setGiftMessage(gme);
        in.setSessionId(wsi.Main.sessionId);
        in.setStore("");
        in.setQuoteId(quoteId);

        GiftMessageForQuoteResponseParam out = wsi.Main.proxy.giftMessageSetForQuote(in);

        if(isLog){
            System.out.println("__________________________________________________");
            System.out.println("Set GiftMessage for quote " + out.getResult().getEntityId() + ":");
            System.out.println(out.getResult().isResult());
            System.out.println("__________________________________________________");
        }
        return out.getResult().isResult();
    }

    public static boolean quoteItem(boolean isLog, String quoteItemId){

        GiftMessageEntity gme = new GiftMessageEntity();
        gme.setFrom("Java");
        gme.setTo("PHP");
        gme.setMessage("Message QuoteItem");

        GiftMessageForQuoteItemRequestParam in = new GiftMessageForQuoteItemRequestParam();
        in.setGiftMessage(gme);
        in.setSessionId(wsi.Main.sessionId);
        in.setStore("");
        in.setQuoteItemId(quoteItemId);

        GiftMessageForQuoteItemResponseParam out = wsi.Main.proxy.giftMessageSetForQuoteItem(in);

        if(isLog){
            System.out.println("__________________________________________________");
            System.out.println("Set GiftMessage for quote item " + out.getResult().getEntityId() + ":");
            System.out.println(out.getResult().isResult());
            System.out.println("__________________________________________________");
        }
        return out.getResult().isResult();
    }

    public static boolean quoteProduct(boolean isLog, String quoteId){

        GiftMessageEntity gme = new GiftMessageEntity();
        gme.setFrom("Java");
        gme.setTo("PHP");
        gme.setMessage("Message QuoteProduct");

        AssociativeEntity ae = new AssociativeEntity();
        ae.setKey("0");
        ae.setValue("1");
        AssociativeEntity ae1 = new AssociativeEntity();
        ae1.setKey("1");
        ae1.setValue("2");

        AssociativeArray aa = new AssociativeArray();
        aa.getComplexObjectArray().add(ae);
        aa.getComplexObjectArray().add(ae1);

        AssociativeEntity ae2 = new AssociativeEntity();
        ae2.setKey("1");
        ae2.setValue("1.0000");

        AssociativeEntity ae3 = new AssociativeEntity();
        ae3.setKey("2");
        ae3.setValue("1.0000");

        AssociativeArray aa1 = new AssociativeArray();
        aa1.getComplexObjectArray().add(ae2);
        aa1.getComplexObjectArray().add(ae3);

        ShoppingCartProductEntity product = new ShoppingCartProductEntity();
        product.setProductId("16");
//        product.setSku("default_bundle_product-default_simple_product-default_virtual_product");
        product.setBundleOption(aa);
        product.setBundleOptionQty(aa1);
        product.setQty(1.0);
//        product.setOptions(aa);
//        product.setLinks(new MWSAPI.ArrayOfString());

        GiftMessageAssociativeProductsEntity gmape = new GiftMessageAssociativeProductsEntity();
        gmape.setMessage(gme);
        gmape.setProduct(product);

        GiftMessageAssociativeProductsEntity gmape1 = new GiftMessageAssociativeProductsEntity();
        gmape1.setMessage(gme);
        gmape1.setProduct(product);

        GiftMessageAssociativeProductsEntityArray gmapea = new GiftMessageAssociativeProductsEntityArray();
        gmapea.getComplexObjectArray().add(gmape);
//        gmapea.getComplexObjectArray().add(gmape1);

        GiftMessageForQuoteProductRequestParam in = new GiftMessageForQuoteProductRequestParam();
        in.setProductsAndMessages(gmapea);
        in.setSessionId(wsi.Main.sessionId);
        in.setStore("1");
        in.setQuoteId(quoteId);

        GiftMessageForQuoteProductResponseParam out = wsi.Main.proxy.giftMessageSetForQuoteProduct(in);

        if(isLog && out.getResult().getComplexObjectArray().size() > 0){
            System.out.println("__________________________________________________");
            System.out.println("Set GiftMessage for quote item " + out.getResult().getComplexObjectArray().get(0).getEntityId() + ":");
            System.out.println(out.getResult().getComplexObjectArray().get(0).getError());
            System.out.println("__________________________________________________");
            return out.getResult().getComplexObjectArray().get(0).isResult();
        }

        return false;
    }

}
