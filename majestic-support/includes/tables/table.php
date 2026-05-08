<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class MJTC_table {

    public $isnew = false;
    public $columns = array();
    public $primarykey = '';
    public $tablename = '';

    function __construct($tbl, $MJTC_pk) {
        $this->tablename = majesticsupport::$_db->prefix . 'mjtc_support_' . $tbl;
        $this->primarykey = $MJTC_pk;
    }

    public function bind($MJTC_data) {
        if ((!is_array($MJTC_data)) || (empty($MJTC_data)))
            return false;
        if (isset($MJTC_data['id']) && !empty($MJTC_data['id'])) { // Edit case
            $this->isnew = false;
        } else { // New case
            $this->isnew = true;
        }
        $MJTC_result = $this->setColumns($MJTC_data);
        return $MJTC_result;
    }

    protected function setColumns($MJTC_data) {
        if ($this->isnew == true) { // new record insert
            $MJTC_array = get_object_vars($this);
            if(isset($MJTC_array['id'])){
                unset($MJTC_array['id']);
            }
            unset($MJTC_array['isnew']);
            unset($MJTC_array['primarykey']);
            unset($MJTC_array['tablename']);
            unset($MJTC_array['columns']);
            foreach ($MJTC_array AS $MJTC_k => $MJTC_v) {
                if (isset($MJTC_data[$MJTC_k])) {
                    $this->$MJTC_k = $MJTC_data[$MJTC_k];
                }
                $this->columns[$MJTC_k] = $this->$MJTC_k;
            }
        } else { // update record
            if (isset($MJTC_data[$this->primarykey])) {
                foreach ($MJTC_data AS $MJTC_k => $MJTC_v) {
                    if (isset($this->$MJTC_k)) {
                        $this->$MJTC_k = $MJTC_v;
                        $this->columns[$MJTC_k] = $MJTC_v;
                    }
                }
            } else {
                return false; // record cannot be updated b/c of pk not exist
            }
        }
        return true;
    }

    function store() {
        if ($this->isnew == true) { // new record store
            majesticsupport::$_db->insert($this->tablename, $this->columns);
            if (majesticsupport::$_db->last_error == null) {
                $this->{$this->primarykey} = majesticsupport::$_db->insert_id;
                $MJTC_id = majesticsupport::$_db->insert_id;
            } else {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                return false;
            }
        } else { // record updated
            majesticsupport::$_db->update($this->tablename, $this->columns, array($this->primarykey => $this->columns[$this->primarykey]));
            if (majesticsupport::$_db->last_error != null) {
                MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
                return false;
            }
        }
        return true;
    }

    function update($MJTC_data) {
        $MJTC_result = $this->bind($MJTC_data);
        if ($MJTC_result == false) {
            return false;
        }
        $MJTC_result = $this->store();
        if ($MJTC_result == false) {
            return false;
        }
        return true;
    }

    function delete($MJTC_id) {
        if (!is_numeric($MJTC_id))
            return false;

        majesticsupport::$_db->delete($this->tablename, array($this->primarykey => $MJTC_id));
        if (majesticsupport::$_db->last_error == null) {
            return true;
        } else {
            MJTC_includer::MJTC_getModel('systemerror')->addSystemError();
            return false;
        }
    }

    function check() {
        return true;
    }

    function load($MJTC_id){
        if(!is_numeric($MJTC_id)) return false;
        $MJTC_query = "SELECT * FROM `".$this->tablename."` WHERE ".esc_sql($this->primarykey)." = ".esc_sql($MJTC_id);
        $MJTC_result = majesticsupport::$_db->get_row($MJTC_query);
        $MJTC_array = get_object_vars($this);
        unset($MJTC_array['isnew']);
        unset($MJTC_array['primarykey']);
        unset($MJTC_array['tablename']);
        unset($MJTC_array['columns']);
        foreach ($MJTC_array AS $MJTC_k => $MJTC_v) {
            if (isset($MJTC_result->$MJTC_k)) {
                $this->$MJTC_k = $MJTC_result->$MJTC_k;
            }
            $this->columns[$MJTC_k] = $this->$MJTC_k;
        }
        return true;
    }

}

?>
