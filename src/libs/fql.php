<?php
/*
 * @creator           : Gordon Lim <honwei189@gmail.com>
 * @created           : 15/05/2019 19:14:39
 * @last modified     : 23/12/2019 22:00:24
 * @last modified by  : Gordon Lim <honwei189@gmail.com>
 */

namespace honwei189\fq;

use \honwei189\flayer as flayer;

/**
 *
 * Faster query language core engine.  A simple and customizable API tools to request and return data
 *
 *
 * @package     fq
 * @subpackage
 * @author      Gordon Lim <honwei189@gmail.com>
 * @link        https://github.com/honwei189/html/
 * @version     "1.0.0" 
 * @since       "1.0.0" 
 */
trait fql
{
    public $action = [
                "add",
                "delete",
                "find",
                "get",
                "save",
                "schema",
                "submit",
                "update",
            ];
    public $query;

    private $schema;
    private $table = __CLASS__;
    
    protected $db;

    /**
     * @access private
     * @internal
     */
    public function construct_fql()
    {
        flayer::fdo()->set_table($this->table);
        $this->db = flayer::fdo();
        // $this->db->set_encrypt_data(true);
        $this->db->set_encrypt_id(true);
    }

    public function add()
    {
        if ($this->build_db_save()) {
            return [$this->query->id];
        } else {
            return [];
        }
    }
    public function delete()
    {
        return $this->build_db_delete();
    }

    /**
     * Find all relavent data from DB
     *
     * @return array
     */
    public function find()
    {
        return $this->query();
    }

    /**
     * Get one data from DB
     *
     * @return array
     */
    public function get()
    {
        return $this->query();
    }
    public function ls()
    {}
    public function save()
    {
        if ($this->build_db_save()) {
            return [$this->query->id];
        } else {
            return [];
        }
    }
    public function submit()
    {}
    /**
     * Get schema for each action function and print out result
     *
     * @return array
     */
    public function schema()
    {
        // $data = null;
        // $get  = (array) $this->query->get; //To not override original data

        // if (is_array($get) && count($get) > 0) {
        //     foreach ($get as $k => $v) {
        //         $data[$v] = $this->get_schema($v);
        //     }
        // }

        return $this->get_schema($this->query->get);
    }
    public function update()
    {
        if ($this->build_db_save()) {
            return [$this->query->id];
        } else {
            return [];
        }
    }

    /**
     * Define action function schema.  Use for request what db table column data
     *
     * @param string $name Data column name
     * @param string $dscpt Description of data column name
     * @param string $real_table_column_name Real data column name in DB table
     * @return object
     */
    protected function define_schema($name, $dscpt, $real_table_column_name = null)
    {
        if (debug_backtrace()[1]['function'] == "__construct") {
            foreach ($this->action as $k => $v) {
                $this->write_schema($v, $name, $dscpt, $real_table_column_name);
            }
        } else {
            $this->write_schema(debug_backtrace()[1]['function'], $name, $dscpt, $real_table_column_name);
        }

        return $this;
    }

    /**
     * Process and get schema for each action function, exclude real_name
     *
     * @param string $key Action request.  E.g:  find, get, save, delete
     * @return array
     */
    protected function get_schema($key)
    {
        $schema = null;

        if (isset($this->schema[$key])) {
            foreach ($this->schema[$key] as $k => $v) {
                $schema[] = $v; //To not override original data
                unset($schema[$k]->real_name);
            }
        }

        return $schema;
    }

    private function build_db_save()
    {
        $this->query->data = (array) $this->query->data;

        if (isset($this->query->data) && is_array($this->query->data) && count($this->query->data) > 0) {
            $cols = array_keys($this->query->data);
            $_    = $this->map_schema($cols);
            $cols = null;

            if (is_array($_)) {
                foreach ($_ as $k => $v) {
                    $cols[$v->name] = ($v->real_name != $v->name ? $v->real_name : $v->name);
                }
            } else {
                $cols[$v->name] = ($_->real_name != $_->name ? $_->real_name : $_->name);
            }

            unset($_);

            if (is_array($cols) && count($cols) > 0) {
                foreach ($cols as $k => $v) {
                    $this->db->$k = $this->query->data[$k];
                }

                if (isset($this->schema["add"]) && is_array($this->schema["add"]) && count($this->schema["add"]) > 0) {
                    foreach ($this->schema["add"] as $k => $v) {
                        switch ($v->name) {
                            case "crdt":
                                $this->db->crdt = "now()";
                                break;

                            case "crdate":
                                $this->db->crdate = "now()";
                                break;

                            case "createdate":
                                $this->db->createdate = "now()";
                                break;

                            case "cdt":
                                $this->db->cdt = "now()";
                                break;

                            case "cdt":
                                $this->db->cdt = "now()";
                                break;
                        }
                    }
                }

                if (isset($this->schema["update"]) && is_array($this->schema["update"]) && count($this->schema["update"]) > 0) {
                    foreach ($this->schema["update"] as $k => $v) {
                        switch ($v->name) {
                            case "lupdate":
                                $this->db->lupdate = "now()";
                                break;

                            case "updatedate":
                                $this->db->updatedate = "now()";
                                break;

                            case "ldt":
                                $this->db->ldt = "now()";
                                break;

                            case "lupdt":
                                $this->db->lupdt = "now()";
                                break;

                            case "lastupdatedate":
                                $this->db->lastupdatedate = "now()";
                                break;
                        }
                    }
                }
            }

            $this->build_db_query(false);

            // $this->db->off_print_format();
            // $this->db->debug();
            // $this->db->passthrough();
            // $this->db->show_sql();

            if (!$this->db->save()) {
                return false;
            } else {
                $this->query->id = flayer::crypto()->encrypt($this->db->_id);
                return true;
            }
        }

        return true;
    }

    private function build_db_delete()
    {
        $this->build_db_query(false);

        // $this->db->off_print_format();
        // $this->db->debug();
        // $this->db->passthrough();
        // $this->db->show_sql();

        return $this->db->delete();
    }

    private function build_db_query($query_only = true)
    {
        if (isset($this->query->id) && is_value($this->query->id)) {
            $this->query->id = (int) flayer::crypto()->decrypt($this->query->id);
            $this->db->by_id($this->query->id);
        }

        // if(isset(flayer::data()::$_user) && is_value(flayer::data()::$_user)){
        //     $this->db->where("");
        // }

        if ($query_only) {
            if (is_value($this->query->select) || is_array($this->query->select) && count($this->query->select) > 0) {
                $cols = [];
                $_    = $this->map_schema($this->query->select);
                if (is_array($_)) {
                    foreach ($_ as $k => $v) {
                        $cols[] = ($v->real_name != $v->name ? $v->real_name . " as " . $v->name : $v->name);
                    }
                } else {
                    $cols[] = ($_->real_name != $_->name ? $_->real_name . " as " . $_->name : $_->name);
                }

                unset($_);

                if (is_array($cols) && count($cols) > 0) {
                    $this->db->cols($cols);
                } else {
                    return false;
                    // return [];
                }
            } else {
                return false;
                // return [];
            }
        }

        if (isset($this->query->sort) && is_value($this->query->sort)) {
            $this->db->order_by($this->query->sort);
        }

        if (is_object($this->query->read)) {
            if ($this->query->read->from > 0 || $this->query->read->to > 0) {
                if ($this->query->read->from > 0 && $this->query->read->to > 0) {
                    $this->db->limit($this->query->read->to, $this->query->read->from);
                } else {
                    if ($this->query->read->from > 0) {
                        $this->db->limit(2147483647, $this->query->read->from);
                    }

                    if ($this->query->read->to > 0) {
                        $this->db->limit($this->query->read->to, 0);
                    }
                }
            }
        }

        if (isset($this->query->search) && (is_value($this->query->search) || is_array($this->query->search) || is_object($this->query->search))) {
            if (is_string($this->query->search) && is_value($this->query->search)) {
                list($cols, $val) = explode("=", $this->query->search);
            } else {
                if(isset($this->query->search->like) && is_object($this->query->search->like)){
                    $this->query->search->like = (array) $this->query->search->like;

                    if(isset($this->query->search->like) && is_array($this->query->search->like) && count($this->query->search->like) > 0){
                        $cols = array_keys($this->query->search->like);
                        $_    = $this->map_schema($cols);
                        $cols = null;

                        if (is_array($_)) {
                            foreach ($_ as $k => $v) {
                                if (isset($this->query->search->like[$v->name])) {
                                    $this->db->where("lower(".$v->real_name.") LIKE '".str_replace("*", "%", strtolower($this->query->search->like[$v->name]))."'");
                                }
                            }
                        }
                    }
                }
                
                if (is_object($this->query->search)) {
                    $this->query->search = (array) $this->query->search;
                }

                $criterias = null;
                $cols      = array_keys($this->query->search);
                $_         = $this->map_schema($cols);
                $cols      = null;

                if (is_array($_)) {
                    foreach ($_ as $k => $v) {
                        if (isset($this->query->search[$v->name])) {
                            $value = $this->query->search[$v->name];

                            if (strpos($value, ",") !== false) {
                                $_cond = array_map(function ($col) {
                                    return trim($col);
                                }, explode(",", $value));

                                foreach ($_cond as $_k => $_v) {
                                    switch (substr($_v, 0, 1)) {
                                        case "!":
                                            $criterias[] = $v->real_name . " != '" . str_replace("!", "", $_v) . "'";
                                            break;

                                        case ">":
                                            if (substr($_v, 0, 2) == ">=") {
                                                $criterias[] = $v->real_name . " >= '" . str_replace(">=", "", $_v) . "'";
                                            } else {
                                                $criterias[] = $v->real_name . " > '" . str_replace(">", "", $_v) . "'";
                                            }
                                            break;

                                        case "<":
                                            if (substr($_v, 0, 2) == "<=") {
                                                $criterias[] = $v->real_name . " <= '" . str_replace("<=", "", $_v) . "'";
                                            } else {
                                                $criterias[] = $v->real_name . " < '" . str_replace("<", "", $_v) . "'";
                                            }
                                            break;

                                        default:
                                            $criterias[] = $v->real_name . " = '" . $_v . "'";
                                            break;
                                    }
                                }

                                unset($_cond);
                            } else {
                                switch (substr($value, 0, 1)) {
                                    case "!":
                                        $criterias[] = $v->real_name . " != '" . str_replace("!", "", $value) . "'";
                                        break;

                                    case ">":
                                        if (substr($value, 0, 2) == ">=") {
                                            $criterias[] = $v->real_name . " >= '" . str_replace(">=", "", $value) . "'";
                                        } else {
                                            $criterias[] = $v->real_name . " > '" . str_replace(">", "", $value) . "'";
                                        }
                                        break;

                                    case "<":
                                        if (substr($value, 0, 2) == "<=") {
                                            $criterias[] = $v->real_name . " <= '" . str_replace("<=", "", $value) . "'";
                                        } else {
                                            $criterias[] = $v->real_name . " < '" . str_replace("<", "", $value) . "'";
                                        }
                                        break;

                                    default:
                                        $criterias[] = $v->real_name . " = '" . $value . "'";
                                        break;
                                }

                            }

                            $value = null;
                        }
                    }

                    $this->db->where($criterias);
                }
            }
        }

        return true;
    }

    /**
     * Get real_name and description from schema.
     *
     * This also to prevent query some table column info which do not want to provide to public, and also to prevent hacking / attack
     *
     * @param string|array $key DB table column name
     * @return array
     */
    private function map_schema($key)
    {
        if (debug_backtrace()[1]['function'] == "build_db_save" || debug_backtrace()[1]['function'] == "build_db_delete" || debug_backtrace()[1]['function'] == "build_db_query") {
            $action = debug_backtrace()[2]['function'];
        } else {
            $action = debug_backtrace()[1]['function'];
        }

        if ($action == "build_db_save" || $action == "build_db_delete") {
            $action = debug_backtrace()[1]['function'];

            if ($action == "build_db_query") {
                $action = "find";
            }
        }

        $action = str_replace("query_", "", $action);

        if (isset($this->schema[$action])) {
            if ($key == "*") {
                return $this->schema[$action];
            } else {
                if (is_string($key) && strpos($key, ",") !== false) {
                    $_ = array_map(function ($col) {
                        return trim($col);
                    }, explode(",", $key));

                    $key = $_;
                    unset($_);
                }

                if (is_array($key) && count($key) > 0) {
                    $return = [];
                    foreach ($this->schema[$action] as $k => $v) {
                        if (in_array($v->name, $key)) {
                            $return[] = $v;
                        }
                    }

                    return $return;
                } else {
                    foreach ($this->schema[$action] as $k => $v) {
                        if ($v->name == $key) {
                            return $v;
                        }
                    }
                }
            }
        }

        return [];
    }

    /**
     * Data query constructor
     *
     * @return array
     */
    protected function query()
    {
        // switch(debug_backtrace()[1]['function']){
        //     case "get":
        //         return $this->query_get();
        //     break;
        // }

        $name = "query_" . debug_backtrace()[1]['function'];
        return $this->$name();
    }

    /**
     * Find all relevant data from DB
     *
     * @return array
     */
    protected function query_find()
    {
        if (!$this->build_db_query()) {
            return [];
        }
        // $this->db->off_print_format();
        // $this->db->debug();
        return $this->db->find();
    }

    /**
     * Get one data from DB
     *
     * @return array
     */
    protected function query_get()
    {
        if (!$this->build_db_query()) {
            return [];
        }

        // $this->db->off_print_format();
        // $this->db->debug();
        return $this->db->get();
    }

    /**
     * Insert data table definition into object array
     *
     * @param string $key Action function name.  e.g:  get, find
     * @param string $name Data column name
     * @param string $dscpt Description of data column name
     * @param string $map_to Real data column name in DB table
     */
    protected function write_schema($key, $name, $dscpt, $map_to = null)
    {
        $idx                                 = (isset($this->schema[$key]) && count($this->schema[$key]) > 0 ? count($this->schema[$key]) : 0);
        $this->schema[$key][$idx]            = (object) null;
        $this->schema[$key][$idx]->name      = $name;
        $this->schema[$key][$idx]->real_name = (is_null($map_to) && !is_value($map_to) ? $name : $map_to);
        $this->schema[$key][$idx]->dscpt     = $dscpt;
    }
}
