<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_pagination {

    private static $_limit;
    private static $_offset;

    static function MJTC_setLimit($MJTC_limit){
        if(is_numeric($MJTC_limit))
            self::$_limit = $MJTC_limit;
    }

    static function MJTC_getLimit(){
        return (int) self::$_limit;
    }

    static function MJTC_setOffset($MJTC_offset){
        if(is_numeric($MJTC_offset))
            self::$_offset = $MJTC_offset;
    }

    static function MJTC_getOffset(){
        return (int) self::$_offset;
    }

    static function MJTC_getPagination($total,$MJTC_layout=null) {
        if(!is_numeric($total)) return false;
        $MJTC_pagenum = isset($_GET['pagenum']) ? majesticsupport::MJTC_sanitizeData(absint($_GET['pagenum'])) : 1; // MJTC_sanitizeData() function uses wordpress santize functions
        if(!self::MJTC_getLimit()){
            self::MJTC_setLimit(majesticsupport::$_config['pagination_default_page_size']); // number of rows in page
        }
        $MJTC_offset = ( $MJTC_pagenum - 1 ) * self::$_limit;
        self::MJTC_setOffset($MJTC_offset);
        $MJTC_num_of_pages = ceil($total / self::$_limit);
        $MJTC_num_of_pages = ($MJTC_num_of_pages > 0) ? ceil($MJTC_num_of_pages) : floor($MJTC_num_of_pages);
        $MJTC_layargs = add_query_arg('pagenum', '%#%');
        $MJTC_list = "";
        $MJTC_list = MJTC_request::MJTC_getVar('list'); //for my ticket only
        if($MJTC_layout != null && get_option( 'permalink_structure' ) != ""){
            if($MJTC_list){
                //$MJTC_layargs = add_query_arg(array('pagenum'=>'%#%' , 'mjtcslay'=>$MJTC_layout));
                $MJTC_layargs = add_query_arg(array('pagenum'=>'%#%' , 'mjtcslay'=>$MJTC_layout, 'list'=>$MJTC_list));
        }else{
                $MJTC_layargs = add_query_arg(array('pagenum'=>'%#%' , 'mjtcslay'=>$MJTC_layout));
            }
        }
        $MJTC_result = paginate_links(array(
            'base' => $MJTC_layargs,
            'format' => '',
            'prev_next' => true,
            'prev_text' => esc_html(__('Previous', 'majestic-support')),
            'next_text' => esc_html(__('Next', 'majestic-support')),
            'total' => $MJTC_num_of_pages,
            'current' => $MJTC_pagenum,
            'add_args' => false,
        ));
        return $MJTC_result;
    }

    static function MJTC_isLastOrdering($total, $MJTC_pagenum) {
        if(!is_numeric($total)) return false;
        if(!is_numeric($MJTC_pagenum)) return false;
        $MJTC_maxrecord = $MJTC_pagenum * majesticsupport::$_config['pagination_default_page_size'];
        if ($MJTC_maxrecord >= $total)
            return false;
        else
            return true;
    }

}

?>
