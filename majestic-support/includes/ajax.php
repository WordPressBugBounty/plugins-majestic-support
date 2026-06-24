<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_ajax {

    function __construct() {
        add_action("wp_ajax_mjsupport_ajax", array($this, "MJTC_ajaxhandler")); // when user is login
        add_action("wp_ajax_nopriv_mjsupport_ajax", array($this, "MJTC_ajaxhandler")); // when user is not login
    }

    function MJTC_ajaxhandler() {
        $MJTC_functions_allowed = array('DataForDepandantField','subscribeForNotifications','unsubscribeFromNotifications','getDownloadById','getAllDownloads','sendTestEmail','getuserlistajax','getmultiformlistajax','getFieldsForComboByFieldFor','getSectionToFillValues','getListTranslations','validateandshowdownloadfilename','getlanguagetranslation','updateUserDevice','checkParentType','checkChildType','makeParentOfType','getTypeForByParentId','getusersearchstaffreportajax','getusersearchuserreportajax','getusersearchajax','saveuserprofileajax','getHelpTopicByDepartment','getpremadeajax','getPremadeByDepartment','getTicketsForMerging','getLatestReplyForMerging','getReplyDataByID','getTimeByReplyID','getTimeByNoteID','readEmailsAjax','getOptionsForFieldEdit','storePrivateCredentials','getFormForPrivteCredentials', 'getPrivateCredentials', 'removePrivateCredential','getWcOrderProductsAjax','markUnmarkTicketNonPremiumAjax','linkTicketPaidSupportAjax','getEDDOrderProductsAjax','getEDDProductlicensesAjax','uploadStaffImage','installPluginFromAjax','activatePluginFromAjax','listEmailTemplate','deleteEmailTemplate','getDefaultEmailTemplate','getHtmlForMoreEmail','getHtmlForMoreConditions','getOperatorsByTitleForCombobox','getValuesByTitleForCombobox','getChildForVisibleCombobox','getConditionsForVisibleCombobox','deleteSupportCustomImage','reviewBoxAction','MJTC_isFieldRequired','getOptionsForEditSlug','checkSmartReply','getSmartReply','getTicketCloseReasonsForPopup','downloadandinstalladdonfromAjax','deleteCategoryLogo','deleteAgentLogo','getHtmlForORRow','getHtmlForANDRow','checkAIReplyTicketsBySubject','markedAsAiPoweredReply','getFilteredReplies','getSmartReplyResponse','mjtc_save_dashboard_layout','getHtmlForAssignPopup','getInternalNoteForEdit','getInstantFixes','scrapeUrlData','instantFixGetFragments','instantFixSync','instantFixDeleteFragment','instantFixUpdateInterval','instantFixToggleStatus','recordInstantFixDeflection','recordInstantFixClick','saveApiKey','savePreferences','syncDataBundle','ajaxGetWrappers','generateReply','delete_log','pgGetCategories','pgGetUseCases','pgGetWrappers','pgGetSchema','pgGetBlockTemplates','pgGetLanguages','pgGetModels','pgExecute','checkPolicyTask','discardDraftTask');
        $MJTC_task = MJTC_request::MJTC_getVar('task');
        if($MJTC_task != '' && in_array($MJTC_task, $MJTC_functions_allowed)){
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
