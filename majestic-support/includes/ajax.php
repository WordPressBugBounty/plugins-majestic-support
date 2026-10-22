<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_ajax {

    function __construct() {
        add_action("wp_ajax_mjsupport_ajax", array($this, "MJTC_ajaxhandler")); // when user is login
        add_action("wp_ajax_nopriv_mjsupport_ajax", array($this, "MJTC_ajaxhandler")); // when user is not login
    }

    function MJTC_ajaxhandler() {
        $MJTC_functions_allowed = array('DataForDepandantField','subscribeForNotifications','unsubscribeFromNotifications','getDownloadById','getAllDownloads','sendTestEmail','getuserlistajax','getmultiformlistajax','getFieldsForComboByFieldFor','getSectionToFillValues','getListTranslations','validateandshowdownloadfilename','getlanguagetranslation','updateUserDevice','checkParentType','checkChildType','makeParentOfType','getTypeForByParentId','getusersearchstaffreportajax','getusersearchuserreportajax','getusersearchajax','saveuserprofileajax','getHelpTopicByDepartment','getpremadeajax','getPremadeByDepartment','getTicketsForMerging','getLatestReplyForMerging','getReplyDataByID','getTimeByReplyID','getTimeByNoteID','readEmailsAjax','getOptionsForFieldEdit','storePrivateCredentials','getFormForPrivteCredentials', 'getPrivateCredentials', 'removePrivateCredential','getWcOrderProductsAjax','markUnmarkTicketNonPremiumAjax','linkTicketPaidSupportAjax','getEDDOrderProductsAjax','getEDDProductlicensesAjax','uploadStaffImage','installPluginFromAjax','activatePluginFromAjax','listEmailTemplate','deleteEmailTemplate','getDefaultEmailTemplate','getHtmlForMoreEmail','getHtmlForMoreConditions','getOperatorsByTitleForCombobox','getValuesByTitleForCombobox','getChildForVisibleCombobox','getConditionsForVisibleCombobox','deleteSupportCustomImage','reviewBoxAction','MJTC_isFieldRequired','getOptionsForEditSlug','checkSmartReply','getSmartReply','getTicketCloseReasonsForPopup','downloadandinstalladdonfromAjax','deleteCategoryLogo','deleteAgentLogo','getHtmlForORRow','getHtmlForANDRow','checkAIReplyTicketsBySubject','markedAsAiPoweredReply','getFilteredReplies','getSmartReplyResponse','mjtc_save_dashboard_layout','getHtmlForAssignPopup','getInternalNoteForEdit','getInstantFixes','scrapeUrlData','instantFixGetFragments','instantFixSync','instantFixDeleteFragment','instantFixUpdateInterval','instantFixToggleStatus','recordInstantFixDeflection','recordInstantFixClick','saveApiKey','savePreferences','syncDataBundle','syncRuntimeModels','ajaxGetWrappers','generateReply','delete_log','pgGetCategories','pgGetUseCases','pgGetWrappers','pgGetSchema','pgGetBlockTemplates','pgGetLanguages','pgGetModels','pgExecute','checkPolicyTask','discardDraftTask');
        /*
         * What a logged-out visitor may call: the ticket form helpers, instant
         * fixes, notification sign-up, ticket closing, public downloads and the
         * private-credentials box on a visitor's own ticket. Every visitor shares
         * one nonce (uid 0), so a nonce proves nothing about which visitor is
         * asking; anything that changes help-desk records is for logged-in users
         * only, and each handler still checks who that user is.
         */
        $MJTC_visitor_functions = array('DataForDepandantField','MJTC_isFieldRequired','getHelpTopicByDepartment','getInstantFixes','recordInstantFixDeflection','recordInstantFixClick','subscribeForNotifications','unsubscribeFromNotifications','updateUserDevice','getTicketCloseReasonsForPopup','getDownloadById','getAllDownloads','storePrivateCredentials','getFormForPrivteCredentials','getPrivateCredentials','removePrivateCredential');
        $MJTC_task = MJTC_request::MJTC_getVar('task');
        if (!is_user_logged_in() && !in_array($MJTC_task, $MJTC_visitor_functions, true)) {
            die('Not Allowed!');
        }
        if($MJTC_task != '' && in_array($MJTC_task, $MJTC_functions_allowed, true)){
            $MJTC_module = MJTC_request::MJTC_getVar('mjsmod');
            $MJTC_module = MJTC_majesticsupportphplib::MJTC_clean_file_path($MJTC_module);
            $MJTC_result = MJTC_includer::MJTC_getModel($MJTC_module)->$MJTC_task();
            echo wp_kses($MJTC_result, MJTC_ALLOWED_TAGS);
            die();
        }else{
            die('Not Allowed!');
        }
    }

}

$MJTC_ajax = new MJTC_ajax();
?>
