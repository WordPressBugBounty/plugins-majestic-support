<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_productModel {

    function getProducts() {
        // Filter
        $MJTC_producttitle = majesticsupport::$_search['product']['product'];
        $MJTC_pagesize = majesticsupport::$_search['product']['pagesize'];
        $MJTC_inquery = '';

        if ($MJTC_producttitle != null){
            $MJTC_inquery .= " WHERE product.product LIKE '%".esc_sql($MJTC_producttitle)."%'";
        }

        majesticsupport::$_data['filter']['title'] = $MJTC_producttitle;
        majesticsupport::$_data['filter']['pagesize'] = $MJTC_pagesize;

        // Pagination
        if($MJTC_pagesize){
            MJTC_pagination::MJTC_setLimit($MJTC_pagesize);
        }
        $MJTC_query = "SELECT COUNT(`id`) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product ";
        $MJTC_query .= $MJTC_inquery;
        $total = majesticsupport::$_db->get_var($MJTC_query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        // Data
        $MJTC_query = "SELECT product.*
					FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product ";
        $MJTC_query .= $MJTC_inquery;
        $MJTC_query .= " ORDER BY product.ordering ASC LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function getProductForCombobox() {
        $MJTC_query = "SELECT id, product AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` WHERE status = 1 ";
        $MJTC_query .= 'ORDER BY ordering ASC';
        $MJTC_products = majesticsupport::$_db->get_results($MJTC_query);

        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_products;
    }

    function getProductForForm($MJTC_id) {
        $MJTC_result=array();
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = "SELECT product.*
				FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product
				WHERE product.id = " . esc_sql($MJTC_id);
            $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            }
        }
        majesticsupport::$_data[0]=$MJTC_result;
        return;
    }

    function storeProduct($MJTC_data) {
        if (!current_user_can('manage_options')) { //only admin can change it.
            return false;
        }
        if (!$this->validateProduct($MJTC_data['product'], $MJTC_data['id'])) {
            MJTC_message::MJTC_setMessage(esc_html(__('Product Title Already Exist', 'majestic-support')), 'error');
            return;
        }
        $MJTC_data = majesticsupport::MJTC_sanitizeData($MJTC_data); // MJTC_sanitizeData() function uses wordpress santize functions

        if (!$MJTC_data['id']) { //new
            $MJTC_data['ordering'] = $this->getNextOrdering();
        }
        $MJTC_row = MJTC_includer::MJTC_getTable('products');
        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
        $MJTC_error = 0;
        if (!$MJTC_row->bind($MJTC_data)) {
            $MJTC_error = 1;
        }
        if (!$MJTC_row->store()) {
            $MJTC_error = 1;
        }

        if ($MJTC_error == 0) {
            $MJTC_id = $MJTC_row->id;
            MJTC_message::MJTC_setMessage(esc_html(__('Product has been stored', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Product has not been stored', 'majestic-support')), 'error');
        }
        return;
    }

    private function validateProduct($MJTC_product, $MJTC_id) {
        if ($MJTC_id) {
            if (!is_numeric($MJTC_id))
                return false;
            $MJTC_query = "SELECT product FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` WHERE id = " . esc_sql($MJTC_id);
            $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
            if ($MJTC_result == $MJTC_product) {
                return true;
            }
        }

        $MJTC_query = 'SELECT COUNT(id) FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_products` WHERE product = "' . esc_sql($MJTC_product) . '"';
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        if ($MJTC_result == 0)
            return true;
        else
            return false;
    }

    private function getNextOrdering() {
        $MJTC_query = "SELECT MAX(ordering) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products`";
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $MJTC_result + 1;
    }

    function removeProduct($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_canremove = $this->canRemoveProduct($MJTC_id);
        if ($MJTC_canremove == 1) {
            $MJTC_row = MJTC_includer::MJTC_getTable('products');
            if ($MJTC_row->delete($MJTC_id)) {
                MJTC_message::MJTC_setMessage(esc_html(__('Product has been deleted', 'majestic-support')), 'updated');
            } else {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
                MJTC_message::MJTC_setMessage(esc_html(__('Product has not been deleted', 'majestic-support')), 'error');
            }
        } elseif ($MJTC_canremove == 2)
            MJTC_message::MJTC_setMessage(esc_html(__('Product','majestic-support')).' '. esc_html(__('in use cannot be deleted', 'majestic-support')), 'error');

        return;
    }

    function setOrdering($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_order = MJTC_request::MJTC_getVar('order', 'get');
        if ($MJTC_order == 'down') {
            $MJTC_order = ">";
            $MJTC_direction = "ASC";
        } else {
            $MJTC_order = "<";
            $MJTC_direction = "DESC";
        }
        $MJTC_query = "SELECT t.ordering,t.id,t2.ordering AS ordering2 FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS t,`" . majesticsupport::$_db->prefix . "mjtc_support_products` AS t2 WHERE t.ordering $MJTC_order t2.ordering AND t2.id = ".esc_sql($MJTC_id)." ORDER BY t.ordering $MJTC_direction LIMIT 1";
        $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);
        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_products` SET ordering = " . esc_sql($MJTC_result->ordering) . " WHERE id = " . esc_sql($MJTC_id);
        majesticsupport::$_db->query($MJTC_query);
        $MJTC_query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_products` SET ordering = " . esc_sql($MJTC_result->ordering2) . " WHERE id = " . esc_sql($MJTC_result->id);
        majesticsupport::$_db->query($MJTC_query);

        $MJTC_row = MJTC_includer::MJTC_getTable('products');
        if ($MJTC_row->update(array('id' => $MJTC_id, 'ordering' => $MJTC_result->ordering)) && $MJTC_row->update(array('id' => $MJTC_result->id, 'ordering' => $MJTC_result->ordering2))) {
            MJTC_message::MJTC_setMessage(esc_html(__('Product','majestic-support')).' '. esc_html(__('ordering has been changed', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Product','majestic-support')).' '. esc_html(__('ordering has not changed', 'majestic-support')), 'error');
        }
        return;
    }

    private function canRemoveProduct($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT (
					(SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE productid = " . esc_sql($MJTC_id) . ")
					) AS total";
        $MJTC_result = majesticsupport::$_db->get_var($MJTC_query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        if ($MJTC_result == 0) {
            return 1;
        } else
            return 2;
    }

    function changeStatus($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;

        $MJTC_query = "SELECT status FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` WHERE id=" . esc_sql($MJTC_id);
        $MJTC_status = majesticsupport::$_db->get_var($MJTC_query);

        $MJTC_status = 1 - $MJTC_status;

        $MJTC_row = MJTC_includer::MJTC_getTable('products');

        if ($MJTC_row->update(array('id' => $MJTC_id, 'status' => $MJTC_status))) {
            MJTC_message::MJTC_setMessage(__('Product','majestic-support').' '.__('status has been changed', 'majestic-support'), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(__('Product','majestic-support').' '.__('status has not been changed', 'majestic-support'), 'error');
        }
        return;
    }

    function getProductById($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;
        $MJTC_query = "SELECT product FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` WHERE id = " . esc_sql($MJTC_id);
        $MJTC_product = majesticsupport::$_db->get_var($MJTC_query);
        return $MJTC_product;
    }

    function getAdminSearchFormDataProduct(){
        $MJTC_nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $MJTC_nonce, 'products') ) {
            die( 'Security check Failed' );
        }
        $ms_search_array = array();
        $title = MJTC_request::MJTC_getVar('title');
        if ($title != '') {
            $ms_search_array['title'] = MJTC_majesticsupportphplib::MJTC_addslashes(MJTC_majesticsupportphplib::MJTC_trim($title));
        } else {
            $ms_search_array['title'] = '';
        }
        $ms_search_array['pagesize'] = absint(MJTC_request::MJTC_getVar('pagesize'));
        $ms_search_array['search_from_product'] = 1;
        return $ms_search_array;
    }
}

?>
