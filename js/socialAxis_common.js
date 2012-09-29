/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */




/*
 Bottom bar js Start here
 */
//jQuery(function(){
//    jQuery("#bottom-bar-innerContainerRightBtmArrow").live('click',function(){
//        RightBtmArrow(jQuery("#bottom-bar-innerContainerRightBtmArrow"));
//        if(!jQuery("#bottom-bar-innerContainerLeftBtmArrow").is(':visible')){
//            LeftTopArrow();
//        }
//    });
//    jQuery("#bottom-bar-innerContainerRightTopArrow").live('click',function(){
//        RightTopArrow();
//    });
//    
//    jQuery("#bottom-bar-innerContainerLeftBtmArrow").live('click',function(){
//        LeftBtmArrow(jQuery("#bottom-bar-innerContainerLeftBtmArrow"));
//        if(!jQuery("#bottom-bar-innerContainerRightBtmArrow").is(':visible')){
//            RightTopArrow();
//        }
//    });
//    jQuery("#bottom-bar-innerContainerLeftTopArrow").live('click',function(){
//        LeftTopArrow()
//    });
//    jQuery("#LoginonBoarding_frontArrow").live('click',function(){
//        loginFullFooter()
//    });
//    jQuery("#bottom-bar-innerContainerSmallDivBackIcon").live('click',function(){
//        if(!jQuery("#bottom-bar-innerContainerRightBtmArrow").is(':visible')){
//            RightTopArrow();
//        }
//        if(!jQuery("#bottom-bar-innerContainerLeftBtmArrow").is(':visible')){
//            LeftTopArrow();
//        }
//        loginSmallFooter()
//    });
//    jQuery('#errorDiv').click(function(){
//        jQuery("#errorDiv").hide(300);
//    });
// 
//});
//
//    
//function RightBtmArrow(ref){
//    jQuery("#bottom-bar-innerContainerRight").animate({
//        opacity: 1,
//        'margin-top':0
//    }, 1000, function() {
//        });
//    ref.hide();
//}
//function RightTopArrow(){
//    jQuery("#bottom-bar-innerContainerRight").animate({
//        opacity: 0,
//        'margin-top':jQuery("#bottom-bar-innerContainerRight").height()+10
//    }, 1000, function() {
//        });
//    jQuery("#bottom-bar-innerContainerRightBtmArrow").show();
//}
//function LeftBtmArrow(ref){
//    jQuery("#bottom-bar-innerContainerLeft").animate({
//        opacity: 1,
//        'margin-top':0
//    }, 1000, function() {
//        });
//    ref.hide();
//}
//function LeftTopArrow(){
//    jQuery("#bottom-bar-innerContainerLeft").animate({
//        opacity: 0,
//        'margin-top':jQuery("#bottom-bar-innerContainerLeft").height()+10
//    }, 1000, function() {
//        });
//    jQuery("#bottom-bar-innerContainerLeftBtmArrow").show();
//}
//
//function loginFullFooter(){
//    jQuery("#bottom-bar-innerContainerSmallDiv").animate({
//        'right': '0px'
//    },10,function(){
//            
//        });
//    jQuery("#bottom-bar-containerMain").animate({
//        'left': jQuery("#bottom-bar-containerMain").width()+20
//    },1000,function(){
//        jQuery("#bottom-bar-containerMain").hide();
//    });
//}
//function loginSmallFooter(){
//    jQuery("#bottom-bar-containerMain").show();
//    jQuery("#bottom-bar-containerMain").animate({
//        'left': '0px'
//    },1000,function(){
//        jQuery("#bottom-bar-innerContainerSmallDiv").animate({
//            'right': '-'+jQuery("#bottom-bar-innerContainerSmallDiv").width()+20
//        },400,function(){
//            
//            });
//            
//    });
//}

/* Bottom Bar Js ends Here*/
//function BetaOut_jsonp_request(){
//    jQuery('#betaoutApiForm').submit();
//    var betaoutApiKey = jQuery('#betaoutApiKey').val();
//    var betaoutApiSecret = jQuery('#betaoutApiSecret').val();
//    var wordpressVersion = jQuery('#wordpressVersion').val();
//    var wordpressBoPluginUrl = jQuery('#wordpressBoPluginUrl').val();
//    jQuery.jsonp({
//        "url": "http://persona.to/api/publication/validatepublication/?callback=?",
//        "data":{
//            'publicationKey':betaoutApiKey,
//            'publicationSecret':betaoutApiSecret,
//            'wordpressVersion':wordpressVersion,
//            'wordpressBoPluginUrl':wordpressBoPluginUrl,
//            'format':'jsonp'
//        },
//        "success": function(data) {
//            if(parseInt(data.responseCode) == 200){
//              
//                jQuery('#betaoutApiForm').submit();
//            }
//           
//            else{
//             
//        }
//        },
//        "error": function() {
//            jQuery("#errorDiv span").text("");
//            jQuery("#errorDiv span").append('Some error during JSONP Request');
//            jQuery("#successDiv").hide(300);
//            showAndHideErrorDiv('errorDiv');
//        }
//    });
  
//}
//function  success()
//{
//    alert('gere');
//    jQuery("#successDiv span").text("");
//    jQuery("#successDiv span").append('publication validated successfully!');
//    jQuery("#errorDiv").hide(300);
//    showAndHideSuccessDiv('successDiv');
//}
//function error(errorMessage){
//    jQuery("#errorDiv span").text("");
//    jQuery("#errorDiv span").append(errorMessage);
//    jQuery("#successDiv").hide(300);
//    showAndHideErrorDiv('errorDiv');
//}
//function showAndHideErrorDiv(errorDivId)
//{
//    jQuery("#"+errorDivId).show(300);
//    setTimeout(function(){
//        jQuery("#"+errorDivId).hide(300);
//    },30000);
//}
//function showAndHideSuccessDiv(successDivId)
//{
//    jQuery("#"+successDivId).show(300);
//    setTimeout(function(){
//        jQuery("#"+successDivId).hide(300);
//    },30000);
//}

