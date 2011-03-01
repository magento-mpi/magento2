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
        int parentCategoryId = 2;

        create(isLog, parentCategoryId);
        update(isLog, createdCategoryId);
        level(isLog, parentCategoryId);
        tree(isLog, parentCategoryId);
        info(isLog, createdCategoryId);
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

    public static boolean update(boolean isLog, int createdCategoryId){

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

    public static int level(boolean isLog, int categoryId){

        CatalogCategoryLevelRequestParam cclrp = new CatalogCategoryLevelRequestParam();
        cclrp.setSessionId(wsi.Main.sessionId);
        String cId = java.lang.Integer.toString(categoryId);
        cclrp.setCategoryId(cId);

        CatalogCategoryLevelResponseParam respons = wsi.Main.proxy.catalogCategoryLevel(cclrp);

        if(isLog){
            System.out.println("__________________________________________________");
            System.out.println("Category (" + categoryId + ") level:");
            System.out.println(respons.getResult().getComplexObjectArray().size());
            System.out.println("__________________________________________________");
        }
        return respons.getResult().getComplexObjectArray().size();
    }

    public static int tree(boolean isLog, int categoryId){

        CatalogCategoryTreeRequestParam cctrp = new CatalogCategoryTreeRequestParam();
        cctrp.setSessionId(wsi.Main.sessionId);
        String cId = java.lang.Integer.toString(categoryId);
        cctrp.setParentId(cId);
        CatalogCategoryTreeResponseParam respons = wsi.Main.proxy.catalogCategoryTree(cctrp);

        if(isLog){
            System.out.println("__________________________________________________");
            System.out.println("Category (" + categoryId + ") tree:");
            System.out.println(respons.getResult().getChildren().getComplexObjectArray().size());
            System.out.println("__________________________________________________");
        }
        return respons.getResult().getChildren().getComplexObjectArray().size();
    }

    public static String info(boolean isLog, int categoryId){

        CatalogCategoryInfoRequestParam cctrp = new CatalogCategoryInfoRequestParam();
        cctrp.setSessionId(wsi.Main.sessionId);
        cctrp.setCategoryId(categoryId);
        CatalogCategoryInfoResponseParam respons = wsi.Main.proxy.catalogCategoryInfo(cctrp);

        if(isLog){
            System.out.println("__________________________________________________");
            System.out.println("Category (" + categoryId + ") tree:");
            System.out.println(respons.getResult().getCategoryId());
            System.out.println("__________________________________________________");
        }
        return respons.getResult().getCategoryId();
    }

}
