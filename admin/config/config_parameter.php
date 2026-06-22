<?php
	
//1. member panel title and company information
$companyinfo="Vastranand";
$companyinfo_city="Surat";  $companyinfo_state="Gujarat";  $companyinfo_country="India";
$softtitle="E-commerce Web Panel";
$footer_developed_by_website_name="https://vastranand.com/";
$footer_developed_by_company_name="Vastranand";
//$define_all_img_base_path_folder="siteuploads/textpagesimage/";                             //if blank then upload category-images, subcategory-images and product-img folder otherwise siteupload/ folder inside
$define_all_img_base_path_folder="";

//2. image upload paremeter for Product , Category, Subcategory, Banner, Banner Offer
$define_product_img_width_height_recommendation="Image Upload By Width:1024px / Height:1024px";    //product img size recommdation
$define_category_img_width_height_recommendation="Image Upload By Width:1000px / Height:300px";    //Category img size recommdation
$define_subcategory_img_width_height_recommendation="Image Upload By Width:1000px / Height:300px";    //Subcategory img size recommdation
$define_banner_img_width_height_recommendation="Image Upload By Width:1349px / Height:450px";    //Banner img size recommdation
$define_banner_offer_img_width_height_recommendation="Image Upload By Width:360px / Height:460px";    //Banner Offer img size recommdation
$define_mobile_app_cat_and_subcat_img_width_height_recommendation="Image Upload By Width:200px / Height:200px";    //Cat and Subcat mobile icon Offer img size recommdation

$define_banner_recommendation_no_of_limit=3;             //Banner Limit
$define_banner_offer_recommendation_no_of_limit=4;      //Banner Offer Limit


$define_product_img_compress_size=1024;                         //product img size compression (width and height auto le se je maximum hoy te ek j le )
$define_product_thumb_img_compress_size=405;                    //product thumb img size compression (width and height auto le se je maximum hoy te ek j le )

$define_mobile_app_cat_and_subcat_option_activation=1;                            //mobile application android and apple cat and subcat img activation 1-Active, 0-Deactive
$define_mobile_app_cat_and_subcat_img_compress_size=400;                         //Mobile app category, subcategory img size compression (width and height auto le se je maximum hoy te ek j le )


//3. Category and Subcategory delete Option and whatsapp Mobile
$define_category_delete_option_activation=1;                      //catgory delete option 1-Active, 0-Deactive
$define_sub_category_delete_option_activation=1;                  //Subcatgory delete option 1-Active, 0-Deactive
$define_category_whatsapp_mobile_activation= 1;                   //Category Whatsapp Mobile activation   1-Active, 0-Deactive
$define_subcategory_whatsapp_mobile_activation= 1;                //Category Whatsapp Mobile activation   1-Active, 0-Deactive
$define_user_delete_option_activation=0;                          //user delete option 1-Active, 0-Deactive


//4. Product Config Parameter (Active 0R Deactive Value)
$define_product_qty_activation=0;                          //product qty insert/update/inventory manage 1-Active, 0-Deactive
$define_product_stockstatus_activation=1;                  //product stcokstatus insert/update/inventory manage 1-Active, 0-Deactive  (Product_qty and product_stockstatus one must be active)
$define_product_out_of_stock_buycart_activation=1;          //1- Always any product buy to cart (in stock or out of stock no matter) , 2 - out of stock product not buy/add to cart 

$define_skucode_activation=1;                       //Option active for Sku Code   1-Active, 0-Deactive
$define_skucode_activation_duplicate_allow=1;       //if 1 Duplicate allow , 2-Not allow Duplicate SKU Code  (if sku code deactive then duplicate allow=1 Must)

$define_moq_activation=0;                          //option active for moq (minimum Order qty) product 
$define_product_score_activation=1;                //option active for Product Score for Weightage of product
if($define_product_score_activation==1){  $define_product_score_weightage_for_product=" product.product_score DESC, "; }

$define_fproduct_activation=0;                          //option active for Featured product   1-Active, 0-Deactive
$define_nproduct_activation=0;                          //option active for New Arrival product   1-Active, 0-Deactive
$define_hotproduct_activation=0;                        //option active for Hot Product product   1-Active, 0-Deactive

$define_product_copy_option_activation=1;                     //option active for Product Copy Activation   1-Active, 0-Deactive
$define_product_additional_filter_create_option_activation=1;                     //option active for Additional Product Filter (Exclude price,Size,Fabrics Type Create Option) Activation   1-Active, 0-Deactive


//Reseller Programme Parameter
$define_reseller_module_activation=1;                               //reseller activation 1-active , 0-deactive
$define_reseller_discount_activation=1;                             //Reseller Discount per product by Rs.   manage  1-Active, 0-Deactive  (reseller discount by sperate product or  discount in % only one must be active)
$define_reseller_discount_by_user_seperate_activation=0;            //Reseller Discount seperate by every user which has under reseller programme  1-active, 0-deactive (reseller discount by sperate product or  discount in % only one must be active)
$define_reseller_discount_show_frontside_allpage_activation=0;      //Reseller discount show fronside every page(category, subcategory, product) 1-Active, 0-Deactive
$define_order_reseller_report_module_activation=1;                                                   // Order reseller report  option activate 1-active , 0-deactive

//Order/checkout auto Fillup by mobile module
$define_cmobile_auto_fillup_module_activation=1;                                              //Order Mobile auto fillup activation 1-active , 0-deactive


//Shipping Charge parameter
$dis_shipping_charge_apply=1;                                // apply Shipping charge 1-Active , 0- None (Deactive)
$define_shipping_type_free_paid_activation=1;                //Option active for product shipping type= free, paid shipping  (if shipping_charge_apply=1  then enabled this)   1-Active, 0-Deactive  (if deactive all product free shipping no shipping charge apply) , (if active and as per charge then shipping apply by state wise)
$define_navigation_shipping_mng_module=1;                         //all Shipping Charge as per State module active or deactive  1-Active, 0-Deactive (if shipping_type_free_paid_activation then apply by state wise rate)
$define_navigation_international_shipping_mng_module=1;                         //all International Shipping Charge as per Country module active or deactive  1-Active, 0-Deactive (Country wise rate)

//COD Charge parameter
$dis_cod_charge_apply=1;                                    // apply cod charge 1-Active , 0- None (Deactive)
$dis_cod_charge_fixed_rate_apply=1;                         // apply cod charge fixed rate 1-Fixed Rate , 2- Statewise charge module active
$dis_cod_charge_fixed_rate_amount=50;                       // Fixed rate amount cod charge as per product qty (if fixed rate apply=1 then Rs apply)
$define_navigation_shipping_cod_per_state_option_activation=0;                         //all COD Charge as per State active or deactive   1-Active, 0-Deactive (if $dis_cod_charge_fixed_rate_apply=2 must)

$dis_cod_charge_partialy_advanced_payment_apply=0;                                             // apply cod partialy advanced payment 1-Active , 0- None (Deactive)
$dis_cod_charge_partialy_advanced_payment_in_percentage=0;                                    // apply cod partialy advanced payment in percentage (20) (grand total) 
$dis_cod_collectable_amount_payment_apply=0;                                                   // apply cod Collectable Amount payment 1-Active , 0- None (Deactive)
$dis_cod_pincode_available_status_activation=1;                                                   // COD Pincode area available status check in frontside 1-Active , 0- None (Deactive)




//5. Menu (other) naivgation Option 
$define_navigation_sms_send_module=1;                             //all sms Module active or deactive  1-Active, 0-Deactive
$define_navigation_announcement_popup_module=0;                   //all Annoucement Popup Activation 1-Active, 0-Deactive
$define_navigation_home_banner_offer_module=1;                    //all Banner Offer active or deactive  1-Active, 0-Deactive
$define_navigation_web_inquiry_module=1;                          //all Web Inquiry module active or deactive  1-Active, 0-Deactive


$define_other_currency_price_show_frontside_module=1;                                //all other currency show price frontside module active or deactive  1-Active, 0-Deactive
$define_other_currency_price_show_frontside_module_limit=100;                          //all other currency show price frontside module limit (2+1) of currency
$define_order_resources_type_option_module=1;                          //Order Resources module  active or deactive  1-Active, 0-Deactive for order kythi avyo te madhyam or stream 




//6. Special Online Payment (fronside online payment by payment gateway) Discount 

$dis_special_online_payment_discount_in_percentage_in_subtotal=0;                                // apply special discount in product/subtotal if payment made by online (not cod), if 0 menas deactive 
$dis_special_online_payment_discount_in_percentage_in_subtotal_details="<p>You Get <span>  Additional Discount</span> Paying through <strong> (DEBIT CARD, CREDIT CARD).</strong></p>";                          // special discount description
$dis_special_online_payment_discount_in_product_flatprice_in_subtotal=0;                                // apply special discount in product create, edit if payment made by online (not cod), if 0 menas deactive (online discount percentage or product flatprice one of them must select)


$dis_couponcode_apply=1;                                                      // apply coupon code 1-Active , 0- None (Deactive)
$define_couponcode_offer_page_with_image=0;                                   // Product Offerzone Page with Image Activation 1-Active , 0- None (Deactive)
$define_couponcode_offer_specific_product_category_subcategory=0;                                   // Offerzone coupon apply specific product, category and subcategory Activation 1-Active , 0- None (Deactive)


// Payment Method and Payment Gateway Details
$payment_method_array=array(1=>'COD', 2=>'Online Payment', 3=>'Bank Transfer', 4=>'Wallet Balance');
//$payment_gateway_company_array=array(1=>'Razorpay', 2=>'Paytm', 0=>'Cashfree');
$payment_gateway_company_array=array(1=>'cashfree',2=>'Phonepay');
$dis_bydefault_payment_method=3;                           // get key of payment method array 
$payment_gateway_paytm_url='https://vastranand.com/payment_api/paytm/pgResponsee';     // paytm callback url

//$define_invoice_no_prefix="ADM";

$define_invoice_no_prefix="APP";                                // invoice prefix
$define_web_invoice_no_prefix="APP";


//frontside product view and after load product count parameter
$reload_product_start_px_product=9; 
$dis_product_limit_view_in_category_start=0;                                          // category page product view total start always must 0
$dis_product_limit_view_in_category_first=24;                                        // category page product view total count product first load 
$dis_product_limit_view_in_category_secondload=24;                                   // category page product view total count product second Load

$dis_product_limit_view_in_subcategory_start=0; 
$dis_product_limit_view_in_subcategory_first=24;                                        // subcategory page product view total count product first load 
$dis_product_limit_view_in_subcategory_secondload=24;                                   // subcategory page product view total count product second Load

$dis_category_filter_showmoreoption_limit=5;                                        // category page product filter showmore option limit after more click


$dis_product_limit_view_in_homepage_start=0;                                         // Home page product view total start always must 0
$dis_product_limit_view_in_homepage_first=24;                                        // Home page product view total count product first load 
$dis_product_limit_view_in_homepage_secondload=24;                                   // Home page product view total count product second Load

if($define_product_score_activation==1){  $define_product_score_weightage_for_product=" product.product_score DESC, "; }    //product as per weightage score
$define_product_out_of_stock_buycart_activation=1;                                   //1- Always any product buy to cart (in stock or out of stock no matter) , 2 - out of stock product not buy/add to cart (Product qty or stockstatus one of two parameter)
$define_cat_subcat_alias_with_last_numeric_id_activation=0;                          //1-Always category/subcategory link with alias numeric id , 0- no alias with numeric (unique cat/subcat name must compulsary)
$define_product_details_page_gst_show_activation=1;                                  //Gst (%) show in product details page frontside  activation  1-active, 0-deactive 
$define_all_page_schema_for_web_promotion_activation=0;                              //all web page promotion schema activation 1-active, 0-deactive
$define_all_page_og_img_for_web_promotion_activation=0;                              //all web page promotion og image for facebook,smo activation 1-active, 0-deactive




//reviews and rating frontside/backside activation
$define_reviews_rating_product_activation=1;                                     //Reviews and Rating activation in product and module (backside) 1-active 0-deactive


//7. GST Charge navigation
$dis_gst_charge_apply=0;                                                           // apply GST charge 1-Active , 0- None (Deactive)
$dis_gst_charge_by_default_fixed=2;                                                //by default fixed rate 1- Active fixed rate, 2-gst apply by seperate product
$dis_gst_charge_by_cgst=2.5;     $dis_gst_charge_by_sgst=2.5;                      //cgst and sgst value put (if default fixed rate)
$define_gst_product_hsncode_module=1;                                              //if this module active ($dis_gst_charge_apply=1 and $dis_gst_charge_by_default_fixed=2)
$define_gst_type_by_stateid_default=8;                                               //gst state by default id from m_state which decide igst or csgst apply


//8. Manage Order on/off control
$define_create_order_search_option_activation=1;                                   //create or edit order search by product/sku  1-active 0-deactive
$define_create_order_search_by_productsku=2;                                       //create or edit order search by   1-product 2-Sku  (if sku must $define_skucode_activation=1)


//11. order management activation 
$define_order_seperate_product_delete_activation=1;                                              //Order Seperate Product delete activation 1-active , 0-deactive
$define_order_seperate_product_copy_activation=1;                                                //order Seperate Product copy  activation 1-active , 0-deactive
$define_order_copy_activation=1;                                                                 //order  copy  activation 1-active , 0-deactive
$define_order_advanced_serach_report_module_activation=1;                                        // Order Search Advanced (docket,orderid, invoiceid, reseller by) report  option activate 1-active , 0-deactive
$define_order_invoice_download_pdf_module_activation=1;                                        // Order invoice download pdf  option activate 1-active , 0-deactive


//advanced order management activation 
$define_advanced_order_reseller_autocomplete_ajax_activation=1;                                  //advanced order reseller autocomplete ajax activation 1-active , 0-deactive          
$define_advanced_order_international_country_activation=1;                                       //advanced order international all country activation 1-active , 0-deactive 
$define_advanced_order_product_additional_parameter_shopcart_filter_activation=0;                //advanced order additional parameter filter(like size, color paramter i/p field) activation 1-active , 0-deactive 
$define_advanced_order_employee_order_id_activation=1;                                           //advanced order employee order suborder id (who person/employee concern with this orderID) activation 1-active , 0-deactive 
$define_advanced_order_product_img_parameter_shopcart_filter_activation=1;                       //advanced order additional img parameter filter(like different product img order) activation 1-active , 0-deactive 

//10. Courier services provider
$define_courier_services_provider_activation=1;                                                            //courier services provider API activation 1-active , 0-deactive
$define_courier_prepaid_docket_allotment_system_activation=1;                                              //courier system docket prepaid auto generated allotment (predefined)  module



//12. courier shipment label Print
$define_courier_shipment_label_company_website_name="Vastranand";
$define_courier_shipment_label_company_Address="Plot No 998,Road No.87, Sachin GIDC ,Near Sutex Bank, Surat, Gujarat, 394230";
$define_courier_shipment_label_company_email="vastranand@gmail.com";
$define_courier_shipment_label_company_mobile=" +91 8154000063";
$define_courier_shipment_label_default_product_name="Cloth And Apparel";

$define_courier_shipment_label_print_sku_limit=3;                                              //max 3 sku limit print in label print otherwise bydefault cloth and apparel option print
$define_courier_shipment_menifest_print_sku_limit=2;                                           //max 3 sku limit print in menifest print otherwise bydefault cloth and apparel option print


//14 return policy module and return order report
$define_return_order_module_activation=1;                                                      // return order module activation  1-active , 0-deactive


//15 Export XlS Order Report
$define_order_xls_export_report_module_activation=0;                                         // Order Report export (Navigation Menu) module activation  1-active , 0-deactive
$define_order_xls_export_report_activation=1;                                                // Order Report export (innerside export pedning, confirm, dispatched, return and order) activation  1-active , 0-deactive

//16 Sales (Employee) Management Module
$define_sales_employee_management_panel_module_activation=1;                                         // Create Sales team and designationwise panel or sales login 1-active , 0-deactive
$define_sales_report_module_activation=1;                                                            //sales report cod,online,bank report and xls file export option activate 1-active , 0-deactive
$define_sales_personwise_report_module_activation=1;                                                 // sales personwise or employeewise report and xls export option activate 1-active , 0-deactive

$define_sales_panel_registered_user_edit_option_activation=1;                                                 // IN Sales Panel registered user edit option option activate 1-active , 0-deactive
$define_sales_panel_all_report_excel_export_option_activation=0;                                              // IN Sales Panel all report export csv excel  option option activate 1-active , 0-deactive

//18 define user array
$define_user_designation_type_array=array(1=>'admin', 2=>'sales', 3=>'customer-care',4=>'manager',5=>'app');



//17 order all footer grand total module
$define_order_report_grandtotal_in_footer_module_activation=1;                                         // all order page grand total and other total (footer) in table activation  1-active , 0-deactive




//Frontside (website) Company Details,address,mobile Parameter
$dis_seller_profile_company_name="Vastranand";
$dis_seller_profile_footer_copyright_company_name="Vastranand";
$dis_seller_profile_header_welcome_message="Welcome To Vastranand.";
$dis_seller_profile_company_mobile="+91 8150400063";
$dis_seller_profile_company_mobile_whatsapp="918154000063";
$dis_seller_profile_company_address="Plot No 998,Road No.87, Sachin GIDC ,Near Sutex Bank, Surat, Gujarat, 394230";
$dis_seller_profile_company_email="vastranand@gmail.com";

$dis_seller_profile_company_mobile_secondary="+91 8154000063 Call Only";
$dis_seller_profile_company_mobile_whatsapp_secondary="";
$dis_seller_profile_company_email_secondary="";
$dis_seller_profile_company_customer_care_mobile="+91 8154000063 ";
$dis_seller_profile_company_gstno="GST123456789M";




$define_company_title_name="vastranand Factory is best seller of Sarees in Surat, India";
$define_company_meta_keywords_name="Get best quality sarees and kurtis from vastranand creations. vastranand Club factory, Surat, Greenland club factory.";
$define_company_meta_description_text="vastranand Creation is best seller of sarees and kurtis in surat get high quality sarees from GL CLUB FACTORY Surat, India";



$define_company_website_url="www.vastranand.com";                               //Company Privacy, Terms Condition, Shipping Page 


//vendor or purchaser seller system module
$define_vendor_sale_purchase_system_module_activation=1;                                                   //Vendor or Purchase module activation
$define_vendor_sale_purchase_accounting_system_module_activation=0;                                        //Vendor or Purchase accounting module activation
$define_vendor_sale_purchase_shipping_charge_system_module_activation=0;                                    //Vendor or Purchase accounting module for shiipping charge activation
$define_vendor_sale_purchase_shipping_charge_cod_system_module_activation=0;                                //Vendor or Purchase accounting module for Cod charge activation
$define_vendor_sale_purchase_gst_charge_system_module_activation=0;                                         //Vendor or Purchase accounting module for GST charge activation

$define_vendor_stock_qty_management_system_module_activation=0;                                                   //Vendor or Purchase Stock Qty module activation
$define_vendor_promotion_activity_registration_management_system_module_activation=0;                              //Vendor or Purchase Promotion Activity Registration module activation

//Agent system module
$define_agent_sale_purchase_system_module_activation=0;                                                     //Agent module activation

//product list for web promotion
$define_product_list_for_web_promotion_module_activation=0;                                                //web promotion product list module activation  1-active , 0-deactive
$define_product_list_for_web_promotion_module_website="https://vastranand.com";                             //web promotion product list module activation  1-active , 0-deactive

//Blog Management
$define_blog_for_webiste_module_activation=1;                                                //website Blog module activation  1-active , 0-deactive

//Website Page Management
$define_webpage_extra_for_webiste_module_activation=0;                                                //website page module activation  1-active , 0-deactive

//Brand Management (Company Brand)
$define_brand_for_product_module_activation=0;                                                //Product Brand module activation  1-active , 0-deactive

//Product Type Filter  Management 
$define_filter_product_type_signle_pcs_catalogue_set_for_product_module_activation=1;                                                //Single Pcs, catalogue, Set Filter module activation  1-active , 0-deactive
$define_filter_product_asc_desc_price_alphabetic_for_product_module_activation=1;                                                    //Category Filter asc, desc pricewise, alphabetic Name Filter module activation  1-active , 0-deactive
$define_filter_product_asc_desc_price_alphabetic_array=array( 1=>array(1=>'Default', 2=>''), 2=>array(1=>'New Arrival', 2=>'ORDER BY product.nproduct DESC ,'.$define_product_score_weightage_for_product.' product.id DESC') , 3=>array(1=>'Price: Lowest First', 2=>'ORDER BY product.sellprice ASC ,'.$define_product_score_weightage_for_product.' product.id DESC'), 4=>array(1=>'Price: Highest First', 2=>'ORDER BY product.sellprice DESC ,'.$define_product_score_weightage_for_product.' product.id DESC') );                            //product sorting by price, name and other in category page activation  1-active , 0-deactive


//Additional Work (Stitching, cloth resize parameter) Array and Module
$define_additional_work_extra_activation=0;                                                   //Additional Extra work module activation  1-active , 0-deactive
$define_additional_work_extra_type_array=array(1=>'Standard Stitching');

//Visitor Appointment Management 
$define_visitor_appointment_management_module_activation=0;                                                //Visitor Appointment Management module activation  1-active , 0-deactive

//Visitor Hotel Booking Management 
$define_visitor_hotel_booking_management_module_activation=0;                                                //Visitor Hotel Booking Management module activation  1-active , 0-deactive

//Order Quotation (PO) Management 
$define_order_quotation_po_management_module_activation=0;                                                //Order Quotation (PO) Management module activation  1-active , 0-deactive

//wallet System Details (credit, debit Ledger)
$define_e_wallet_credit_debit_account_module_activation=1;                                                //Wallet System Activation  1-active , 0-deactive

$define_email_confirmation_auto_send_module_activation=1;
$define_email_confirmation_auto_send_header='Vastranand Order <order@vastranand.com>'; 
$define_email_replay='vastranand@gmail.com'; 

$define_authorised_login_system_module_activation=0;                                    //authorised token system activation for admin/sales panel
$define_authorised_login_system_mobile_array=array(1=>9712566025, 2=>9924272574);       //authorised token system otp Mobile
$define_authorised_login_system_module_token1='ATOP876A';                                    //authorised token1
$define_authorised_login_system_module_token2='Pa34897B';                                    //authorised token2

$login_otp_number_list='9712566025,9924272574';
$define_wallet_creadit_amount=50;
$define_reseller_max_margin='250';
$define_Return_order_days='7';

?>