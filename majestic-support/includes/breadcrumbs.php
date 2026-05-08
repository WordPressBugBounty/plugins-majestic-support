<?php
if (!defined('ABSPATH'))
    die('Restricted Access');
class MJTC_breadcrumbs {

    static function MJTC_getBreadcrumbs() {
        if (majesticsupport::$_config['show_breadcrumbs'] != 1)
            return false;
        if (!is_admin()) {
            $MJTC_editid = MJTC_request::MJTC_getVar('majesticsupportid');
            $MJTC_isnew = ($MJTC_editid == null) ? true : false;
            $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>'majesticsupport', 'mjslay'=>'controlpanel')), 'text' => esc_html(__('Control Panel', 'majestic-support')));
            $MJTC_module = MJTC_request::MJTC_getVar('mjsmod');
            $MJTC_layout = MJTC_request::MJTC_getVar('mjslay');
            if (isset(majesticsupport::$_data['short_code_header'])) {
                switch (majesticsupport::$_data['short_code_header']){
                    case 'myticket':

                        $MJTC_module = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'agent' : 'ticket';
                        $MJTC_layout = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'staffmyticket' : 'myticket';
                        break;
                    case 'addticket':
                        $MJTC_module = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'agent' : 'ticket';
                        $MJTC_layout = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'staffaddticket' : 'addticket';
                        break;
                    case 'downloads':
                        $MJTC_module = 'download';
                        $MJTC_layout = 'downloads';
                        break;
                    case 'faqs':
                        $MJTC_module = 'faq';
                        $MJTC_layout = 'faqs';
                        break;
                    case 'announcements':
                        $MJTC_module = 'announcement';
                        $MJTC_layout = 'announcements';
                        break;
                    case 'userknowledgebase':
                        $MJTC_module = 'knowledgebase';
                        $MJTC_layout = 'userknowledgebase';
                        break;
                }
            }

            if ($MJTC_module != null) {
                switch ($MJTC_module) {
                    case 'announcement':
                        switch ($MJTC_layout) {
                            case 'announcements':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Announcements', 'majestic-support')));
                                break;
                            case 'announcementdetails':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Announcement Detail', 'majestic-support')));
                                break;
                            case 'addannouncement':
                                $MJTC_layout1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'staffannouncements' : 'announcements';
                                $MJTC_text = ($MJTC_isnew) ? esc_html(__('Add Announcement', 'majestic-support')) : esc_html(__('Edit Announcement', 'majestic-support'));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => $MJTC_text);
                                break;
                            case 'staffannouncements':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Announcements', 'majestic-support')));
                                break;
                        }
                        break;
                    case 'department':
                        switch ($MJTC_layout) {
                            case 'adddepartment':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>'departments')), 'text' => esc_html(__('Departments', 'majestic-support')));
                                $MJTC_text = ($MJTC_isnew) ? esc_html(__('Add Department', 'majestic-support')) : esc_html(__('Edit Department', 'majestic-support'));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => $MJTC_text);
                                break;
                            case 'departments':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Departments', 'majestic-support')));
                                break;
                        }
                        break;
                    case 'reports':
                        switch ($MJTC_layout) {
                            case 'staffdetailreport':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Staff Reports', 'majestic-support')));
                                break;
                            case 'staffreports':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Staff reports', 'majestic-support')));
                                break;
                            case 'departmentreports':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Departments report', 'majestic-support')));
                                break;
                        }
                        break;
                    case 'download':
                        switch ($MJTC_layout) {
                            case 'adddownload':
                                $MJTC_layout1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'staffdownloads' : 'downloads';
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout1)), 'text' => esc_html(__('Downloads', 'majestic-support')));
                                $MJTC_text = ($MJTC_isnew) ? esc_html(__('Add Download', 'majestic-support')) : esc_html(__('Edit Download', 'majestic-support'));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => $MJTC_text);
                                break;
                            case 'downloads':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Downloads', 'majestic-support')));
                                break;
                            case 'staffdownloads':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Downloads', 'majestic-support')));
                                break;
                        }
                        break;
                    case 'faq':
                        switch ($MJTC_layout) {
                            case 'addfaq':
                                $MJTC_layout1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'stafffaqs' : 'faqs';
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout1)), 'text' => esc_html(__("FAQs", 'majestic-support')));
                                $MJTC_text = ($MJTC_isnew) ? esc_html(__('Add FAQ', 'majestic-support')) : esc_html(__('Edit FAQ', 'majestic-support'));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => $MJTC_text);
                                break;
                            case 'faqdetails':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('FAQ Detail', 'majestic-support')));
                                break;
                            case 'faqs':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__("FAQs", 'majestic-support')));
                                break;
                            case 'stafffaqs':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__("FAQs", 'majestic-support')));
                                break;
                        }
                        break;
                    case 'feedback':
                        switch ($MJTC_layout) {
                            case 'feedbacks':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>'feedback', 'mjslay'=>'feedbacks')), 'text' => esc_html(__("Feedback", 'majestic-support')));
                                break;
                        }
                        break;
                    case 'majesticsupport':
                        break;
                    case 'knowledgebase':
                        switch ($MJTC_layout) {
                            case 'addarticle':
                                $MJTC_text = ($MJTC_isnew) ? esc_html(__('Add Knowledge Base', 'majestic-support')) : esc_html(__('Edit Knowledge Base', 'majestic-support'));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => $MJTC_text);
                                break;
                            case 'addcategory':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>'stafflistcategories')), 'text' => esc_html(__('Categories', 'majestic-support')));
                                $MJTC_text = ($MJTC_isnew) ? esc_html(__('Add Category', 'majestic-support')) : esc_html(__('Edit Category', 'majestic-support'));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => $MJTC_text);
                                break;
                            case 'articledetails':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Knowledge Base Detail', 'majestic-support')));
                                break;
                            case 'listarticles':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Knowledge Base', 'majestic-support')));
                                break;
                            case 'listcategories':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Categories', 'majestic-support')));
                                break;
                            case 'stafflistarticles':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Knowledge Base', 'majestic-support')));
                                break;
                            case 'stafflistcategories':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Categories', 'majestic-support')));
                                break;
                            case 'userknowledgebase':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Knowledge Base', 'majestic-support')));
                                break;
                            case 'userknowledgebasearticles':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Knowledge Base', 'majestic-support')));
                                break;
                        }
                        break;
                    case 'mail':
                        switch ($MJTC_layout) {
                            case 'formmessage':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Message', 'majestic-support')));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Send Message', 'majestic-support')));
                                break;
                            case 'inbox':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Message', 'majestic-support')));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Inbox', 'majestic-support')));
                                break;
                            case 'outbox':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Message', 'majestic-support')));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Outbox', 'majestic-support')));
                                break;
                            case 'message':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>'inbox')), 'text' => esc_html(__('Message', 'majestic-support')));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Message', 'majestic-support')));
                                break;
                        }
                        break;
                    case 'role':
                        switch ($MJTC_layout) {
                            case 'addrole':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>'roles')), 'text' => esc_html(__('Roles', 'majestic-support')));
                                $MJTC_text = ($MJTC_isnew) ? esc_html(__('Add Role', 'majestic-support')) : esc_html(__('Edit Role', 'majestic-support'));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => $MJTC_text);
                                break;
                            case 'rolepermission':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>'roles')), 'text' => esc_html(__('Roles', 'majestic-support')));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Role Permissions', 'majestic-support')));
                                break;
                            case 'roles':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Roles', 'majestic-support')));
                                break;
                        }
                        break;
                    case 'agent':
                        switch ($MJTC_layout) {
                            case 'addstaff':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>'staffs')), 'text' => esc_html(__('Staffs', 'majestic-support')));
                                $MJTC_text = ($MJTC_isnew) ? esc_html(__('Add Staff', 'majestic-support')) : esc_html(__('Edit Staff', 'majestic-support'));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => $MJTC_text);
                                break;
                            case 'staffpermissions':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Staff Permissions', 'majestic-support')));
                                break;
                            case 'staffs':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module, 'mjslay'=>$MJTC_layout)), 'text' => esc_html(__('Staffs', 'majestic-support')));
                                break;
                        }
                        break;
                    case 'ticket':
                        // Add default module link
                        switch ($MJTC_layout) {
                            case 'addticket':
                                $MJTC_layout1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'staffmyticket':'myticket';
                                $MJTC_module1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'agent':'ticket';
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module1, 'mjslay'=>$MJTC_layout1)), 'text'=>esc_html(__('My Tickets','majestic-support')));
                                $MJTC_text = ($MJTC_isnew) ? esc_html(__('Add Ticket', 'majestic-support')) : esc_html(__('Edit Ticket', 'majestic-support'));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'addticket')), 'text' => $MJTC_text);
                                break;
                            case 'myticket':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'myticket')), 'text' => esc_html(__('My Tickets', 'majestic-support')));
                                break;
                            case 'staffaddticket':
                                $MJTC_layout1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'staffmyticket':'myticket';
                                $MJTC_module1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'agent':'ticket';
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module1, 'mjslay'=>$MJTC_layout1)), 'text'=>esc_html(__('My Tickets','majestic-support')));
                                $MJTC_text = ($MJTC_isnew) ? esc_html(__('Add Ticket', 'majestic-support')) : esc_html(__('Edit Ticket', 'majestic-support'));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffaddticket')), 'text' => $MJTC_text);
                                break;
                            case 'staffmyticket':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>'agent', 'mjslay'=>'staffmyticket')), 'text' => esc_html(__('My Tickets', 'majestic-support')));
                                break;
                            case 'ticketdetail':
                                $MJTC_layout1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'staffmyticket' : 'myticket';
                                $MJTC_module1 = ( in_array('agent',majesticsupport::$_active_addons) && MJTC_includer::MJTC_getModel('agent')->isUserStaff()) ? 'agent' : 'ticket';
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>$MJTC_module1, 'mjslay'=>$MJTC_layout1)), 'text'=>esc_html(__('My Tickets','majestic-support')));
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketdetail')), 'text' => esc_html(__('Ticket Detail', 'majestic-support')));
                                break;
                            case 'ticketstatus':
                                $MJTC_array[] = array('link' => majesticsupport::makeUrl(array('mjsmod'=>'ticket', 'mjslay'=>'ticketstatus')), 'text' => esc_html(__('Ticket Status', 'majestic-support')));
                                break;
                        }
                        break;
                }
            }
        }

        if (isset($MJTC_array)) {
            $MJTC_count = count($MJTC_array);
            $MJTC_i = 0;
            $MJTC_html = '<div class="mjtc-support-breadcrumb-wrp">
                    <ul class="breadcrumb mjtc-support-breadcrumb">';
                        foreach ($MJTC_array AS $MJTC_obj) {
                            if ($MJTC_i == 0) {
                                $MJTC_html .= '
                                <li>
                                    <a href="' . esc_url($MJTC_obj['link']) . '">
                                        <img class="homeicon" alt="'.esc_attr(__('home icon', 'majestic-support')).'" src="' . esc_url(MJTC_PLUGIN_URL) . 'includes/images/homeicon-white.png"/>
                                    </a>
                                </li>';
                            } else {
                                if ($MJTC_i == ($MJTC_count - 1)) {
                                    $MJTC_html .= '
                                    <li>
                                        <a href="">
                                            ' . esc_html($MJTC_obj['text']) . '
                                        </a>
                                    </li>';
                                } else {
                                    $MJTC_html .= '
                                    <li>
                                        <a href="' . esc_url($MJTC_obj['link']) . '">
                                            ' . esc_html($MJTC_obj['text']) . '
                                        </a>
                                    </li>';
                                }
                            }
                        $MJTC_i++;
                        }
            $MJTC_html .= ' </ul>
                </div>';
            echo wp_kses($MJTC_html, MJTC_ALLOWED_TAGS);
        }
    }

}

$MJTC_breadcrumbs = new MJTC_breadcrumbs;
?>
