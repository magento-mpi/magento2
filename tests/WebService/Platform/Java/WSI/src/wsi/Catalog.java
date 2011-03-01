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
public class Catalog {

    Catalog(boolean isLog){
        
        create(isLog, 2);
        update(isLog);
        level(isLog);
    }

    private static int createdCategoryId;

    public static int create(boolean isLog, int parentId){
        
        ArrayOfString asb = new ArrayOfString();
        asb.getComplexObjectArray().add("price");
        asb.getComplexObjectArray().add("name");

        CatalogCategoryEntityCreate ccec = new CatalogCategoryEntityCreate();
        ccec.setAvailableSortBy(asb);
        ccec.setDefaultSortBy("name");
        ccec.setDescription("Catalog description");
        ccec.setIncludeInMenu(1);
        ccec.setIsActive(1);
        ccec.setName("Java created");
        ccec.setMetaDescription("Meta description from Java");
        ccec.setMetaKeywords("java, app");
        ccec.setMetaTitle("Java meta title");
        
        CatalogCategoryCreateRequestParam cccrp = new CatalogCategoryCreateRequestParam();
        cccrp.setSessionId(wsi.Main.sessionId);
        cccrp.setParentId(parentId);
        cccrp.setStore("");
        cccrp.setCategoryData(ccec);

        CatalogCategoryCreateResponseParam respons = wsi.Main.proxy.catalogCategoryCreate(cccrp);

        createdCategoryId = respons.getResult();

        if(isLog){
            System.out.println("__________________________________________________");
            System.out.println("createdCategoryId:");
            System.out.println(respons.getResult());
            System.out.println("__________________________________________________");
        }
        return createdCategoryId;
    }

    public static boolean update(boolean isLog){

        ArrayOfString asb = new ArrayOfString();
        asb.getComplexObjectArray().add("price");
//        asb.getComplexObjectArray().add("name");

        CatalogCategoryEntityCreate ccec = new CatalogCategoryEntityCreate();
        ccec.setAvailableSortBy(asb);
        ccec.setDefaultSortBy("price");
        ccec.setDescription("Catalog description");
        ccec.setIncludeInMenu(1);
        ccec.setIsActive(1);
        ccec.setName("Java updated");
        ccec.setMetaDescription("Meta description from Java");
        ccec.setMetaKeywords("java, app");
        ccec.setMetaTitle("Java meta title");

        CatalogCategoryUpdateRequestParam cccup = new CatalogCategoryUpdateRequestParam();
        cccup.setSessionId(wsi.Main.sessionId);
        cccup.setStore("");
        cccup.setCategoryData(ccec);
        cccup.setCategoryId(createdCategoryId);

        CatalogCategoryUpdateResponseParam respons = wsi.Main.proxy.catalogCategoryUpdate(cccup);

        if(isLog){
            System.out.println("__________________________________________________");
            System.out.println("Was Category (" + createdCategoryId + ") updated?:");
            System.out.println(respons.isResult());
            System.out.println("__________________________________________________");
        }
        return respons.isResult();
    }

    public static void level(boolean isLog){

//        ArrayOfString asb = new ArrayOfString();
//        asb.getComplexObjectArray().add("price");
////        asb.getComplexObjectArray().add("name");
//
//        CatalogCategoryEntityCreate ccec = new CatalogCategoryEntityCreate();
//        ccec.setAvailableSortBy(asb);
//        ccec.setDefaultSortBy("price");
//        ccec.setDescription("Catalog description");
//        ccec.setIncludeInMenu(1);
//        ccec.setIsActive(1);
//        ccec.setName("Java updated");
//        ccec.setMetaDescription("Meta description from Java");
//        ccec.setMetaKeywords("java, app");
//        ccec.setMetaTitle("Java meta title");
//
//        CatalogCategoryUpdateRequestParam cccup = new CatalogCategoryUpdateRequestParam();
//        cccup.setSessionId(wsi.Main.sessionId);
//        cccup.setStore("");
//        cccup.setCategoryData(ccec);
//        cccup.setCategoryId(createdCategoryId);
//
//        CatalogCategoryUpdateResponseParam respons = wsi.Main.proxy.catalogCategoryUpdate(cccup);

        CatalogCategoryLevelRequestParam cclrp = new CatalogCategoryLevelRequestParam();
        cclrp.setSessionId(wsi.Main.sessionId);
        String cId = java.lang.Integer.toString(createdCategoryId);
        cclrp.setCategoryId(cId);

        CatalogCategoryLevelResponseParam respons = wsi.Main.proxy.catalogCategoryLevel(cclrp);

        if(isLog){
            System.out.println("__________________________________________________");
            System.out.println("Was Category (" + createdCategoryId + ") updated:");
            System.out.println(respons.getResult());
            System.out.println("__________________________________________________");
        }
    }

}
