<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_productModel {

    function getProducts() {
        // Filter
        $producttitle = majesticsupport::$_search['product']['product'];
        $pagesize = majesticsupport::$_search['product']['pagesize'];
        $inquery = '';

        if ($producttitle != null){
            $inquery .= " WHERE product.product LIKE '%".esc_sql($producttitle)."%'";
        }

        majesticsupport::$_data['filter']['title'] = $producttitle;
        majesticsupport::$_data['filter']['pagesize'] = $pagesize;

        // Pagination
        if($pagesize){
            MJTC_pagination::MJTC_setLimit($pagesize);
        }
        $query = "SELECT COUNT(`id`) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product ";
        $query .= $inquery;
        $total = majesticsupport::$_db->get_var($query);
        majesticsupport::$_data['total'] = $total;
        majesticsupport::$_data[1] = MJTC_pagination::MJTC_getPagination($total);

        // Data
        $query = "SELECT product.*
					FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product ";
        $query .= $inquery;
        $query .= " ORDER BY product.ordering ASC LIMIT " . MJTC_pagination::MJTC_getOffset() . ", " . MJTC_pagination::MJTC_getLimit();
        majesticsupport::$_data[0] = majesticsupport::$_db->get_results($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return;
    }

    function getProductForCombobox() {
        $query = "SELECT id, product AS text FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` WHERE status = 1 ";
        $query .= 'ORDER BY ordering ASC';
        $products = majesticsupport::$_db->get_results($query);

        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $products;
    }

    function getProductForForm($id) {
        $result=array();
        if ($id) {
            if (!is_numeric($id))
                return false;
            $query = "SELECT product.*
				FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS product
				WHERE product.id = " . esc_sql($id);
            $result = majesticsupport::$_db->get_row($query);
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            }
        }
        majesticsupport::$_data[0]=$result;
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
        $row = MJTC_includer::MJTC_getTable('products');
        $MJTC_data = MJTC_includer::MJTC_getModel('majesticsupport')->stripslashesFull($MJTC_data);// remove slashes with quotes.
        $error = 0;
        if (!$row->bind($MJTC_data)) {
            $error = 1;
        }
        if (!$row->store()) {
            $error = 1;
        }

        if ($error == 0) {
            $id = $row->id;
            MJTC_message::MJTC_setMessage(esc_html(__('Product has been stored', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Product has not been stored', 'majestic-support')), 'error');
        }
        return;
    }

    private function validateProduct($product, $id) {
        if ($id) {
            if (!is_numeric($id))
                return false;
            $query = "SELECT product FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` WHERE id = " . esc_sql($id);
            $result = majesticsupport::$_db->get_var($query);
            if ($result == $product) {
                return true;
            }
        }

        $query = 'SELECT COUNT(id) FROM `' . majesticsupport::$_db->prefix . 'mjtc_support_products` WHERE product = "' . esc_sql($product) . '"';
        $result = majesticsupport::$_db->get_var($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        if ($result == 0)
            return true;
        else
            return false;
    }

    private function getNextOrdering() {
        $query = "SELECT MAX(ordering) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products`";
        $result = majesticsupport::$_db->get_var($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        return $result + 1;
    }

    function removeProduct($id) {
        if (!is_numeric($id))
            return false;
        $canremove = $this->canRemoveProduct($id);
        if ($canremove == 1) {
            $row = MJTC_includer::MJTC_getTable('products');
            if ($row->delete($id)) {
                MJTC_message::MJTC_setMessage(esc_html(__('Product has been deleted', 'majestic-support')), 'updated');
            } else {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
                MJTC_message::MJTC_setMessage(esc_html(__('Product has not been deleted', 'majestic-support')), 'error');
            }
        } elseif ($canremove == 2)
            MJTC_message::MJTC_setMessage(esc_html(__('Product','majestic-support')).' '. esc_html(__('in use cannot deleted', 'majestic-support')), 'error');

        return;
    }

    function setOrdering($id) {
        if (!is_numeric($id))
            return false;
        $order = MJTC_request::MJTC_getVar('order', 'get');
        if ($order == 'down') {
            $order = ">";
            $direction = "ASC";
        } else {
            $order = "<";
            $direction = "DESC";
        }
        $query = "SELECT t.ordering,t.id,t2.ordering AS ordering2 FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` AS t,`" . majesticsupport::$_db->prefix . "mjtc_support_products` AS t2 WHERE t.ordering $order t2.ordering AND t2.id = ".esc_sql($id)." ORDER BY t.ordering $direction LIMIT 1";
        $result = majesticsupport::$_db->get_row($query);
        $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_products` SET ordering = " . esc_sql($result->ordering) . " WHERE id = " . esc_sql($id);
        majesticsupport::$_db->query($query);
        $query = "UPDATE `" . majesticsupport::$_db->prefix . "mjtc_support_products` SET ordering = " . esc_sql($result->ordering2) . " WHERE id = " . esc_sql($result->id);
        majesticsupport::$_db->query($query);

        $row = MJTC_includer::MJTC_getTable('products');
        if ($row->update(array('id' => $id, 'ordering' => $result->ordering)) && $row->update(array('id' => $result->id, 'ordering' => $result->ordering2))) {
            MJTC_message::MJTC_setMessage(esc_html(__('Product','majestic-support')).' '. esc_html(__('ordering has been changed', 'majestic-support')), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(esc_html(__('Product','majestic-support')).' '. esc_html(__('ordering has not changed', 'majestic-support')), 'error');
        }
        return;
    }

    private function canRemoveProduct($id) {
        if (!is_numeric($id))
            return false;
        $query = "SELECT (
					(SELECT COUNT(id) FROM `" . majesticsupport::$_db->prefix . "mjtc_support_tickets` WHERE productid = " . esc_sql($id) . ")
					) AS total";
        $result = majesticsupport::$_db->get_var($query);
        if (majesticsupport::$_db->last_error != null) {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
        }
        if ($result == 0) {
            return 1;
        } else
            return 2;
    }

    function changeStatus($id) {
        if (!is_numeric($id))
            return false;

        $query = "SELECT status FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` WHERE id=" . esc_sql($id);
        $status = majesticsupport::$_db->get_var($query);

        $status = 1 - $status;

        $row = MJTC_includer::MJTC_getTable('products');

        if ($row->update(array('id' => $id, 'status' => $status))) {
            MJTC_message::MJTC_setMessage(__('Product','majestic-support').' '.__('status has been changed', 'majestic-support'), 'updated');
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            MJTC_message::MJTC_setMessage(__('Product','majestic-support').' '.__('status has not been changed', 'majestic-support'), 'error');
        }
        return;
    }

    function getProductById($id) {
        if (!is_numeric($id))
            return false;
        $query = "SELECT product FROM `" . majesticsupport::$_db->prefix . "mjtc_support_products` WHERE id = " . esc_sql($id);
        $product = majesticsupport::$_db->get_var($query);
        return $product;
    }

    function getAdminSearchFormDataProduct(){
        $nonce = MJTC_request::MJTC_getVar('_wpnonce');
        if (! wp_verify_nonce( $nonce, 'products') ) {
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
