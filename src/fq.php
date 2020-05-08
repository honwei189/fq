<?php
/*
 * @creator           : Gordon Lim <honwei189@gmail.com>
 * @created           : 13/05/2019 19:32:39
 * @last modified     : 22/03/2020 22:11:27
 * @last modified by  : Gordon Lim <honwei189@gmail.com>
 */

namespace honwei189\fq;

use \honwei189\config as config;
use \honwei189\flayer as flayer;

// use \honwei189\http as http;

/**
 *
 * Fast Query ( Simple Web Query Language ).
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
class fq
{
    public $action;
    public $http;
    public $predefined;

    private $base_path;
    private $custom_action;
    private $path;
    private $query = [];

    /**
     * @access private
     * @internal
     */
    public function __construct()
    {
        // $this->http = new http;
        $this->http = flayer::bind("\\honwei189\\http");

        $this->action = [
            "add",
            "delete",
            "find",
            "get",
            "save",
            "schema",
            "submit",
            "update",
        ];

        $this->predefined    = ["action", "custom", "data", "exec", "fetch", "id", "method", "read", "search", "select", "sort", "table", "where"];
        $this->custom_action = ["action", "exec"];

        if (is_null($this->path)) {
            if (php_sapi_name() == "cli") {
                $this->base_path = realpath(__DIR__ . (isset($_SERVER['SHELL']) ? "/../../../../app/" : "../../../../../app/"));
            } else {
                $this->base_path = realpath(substr_replace($_SERVER['DOCUMENT_ROOT'], "", strrpos($_SERVER['DOCUMENT_ROOT'], "public")) . "app/");
            }
        }

        $this->path = $this->base_path . DIRECTORY_SEPARATOR;

        // $this->query[] = (object) [
        //     "method" => "",
        //     "table"  => "",
        //     "search" => "",
        //     "select" => "",
        //     "read"  => "",
        //     "get"    => "",
        //     "data"   => "",
        //     "sort"   => "",
        //     "id"     => null,
        // ];
    }

    public function bootstrap()
    {
        if (is_array($this->http->_json) && count($this->http->_json) > 0) {
            $this->analyze($this->http->_json);
            $data                 = null;
            $query                = null;
            $query_jwt_has_failed = false;
            $pass                 = true;
            $send_jwt_to_client   = false;
            $class_method         = "";
            $i                    = 0;

            foreach ($this->query as $k => $v) {
                list($action, ) = explode(":", $k);

                if (is_object($v)) {
                    $query_action   = $action;
                    $query          = $k;
                    $instance_name  = "fq_" . str_replace(":", "_", str_replace("/", "_", $k));
                    $request_action = str_replace(":", "_", str_replace("/", "_", $k));
                    
                    if (strpos($v->namespace, "/") !== false) {
                        $class = substr(strrchr($v->namespace, "/"), 1);
                    } else {
                        $class = $v->namespace;
                    }

                    if (strpos($class, ":") !== false) {
                        list($class, $class_method) = explode(":", $class);
                    }

                    if ($this->http->is_jwt_auth) {
                        $config = config::get("fq", "jwt");
                        $pass   = $this->http->is_jwt_auth_success;

                        if (isset($config['exclude']) && is_array($config['exclude']) && count($config['exclude']) > 0) {
                            for ($i = 0; $max = count($config['exclude']), $i < $max; ++$i) {
                                switch ($config['exclude'][$i]) {
                                    case "*":
                                        $pass = true;
                                        $i    = $max;
                                        break;

                                    case "":
                                        if (!is_value($query)) {
                                            $pass = true;
                                            $i    = $max;
                                            break;
                                        }
                                        break;

                                    default:
                                        if (stripos($config['exclude'][$i], "*") !== false) {
                                            // wildcard.  Allows all API with the prefix name
                                            $pattern = str_replace("*", "(.*)", $config['exclude'][$i]);

                                            if (preg_match("|$pattern|si", $query)) {
                                                $pass = true;
                                                $i    = $max;
                                            }
                                        } else if (stripos($config['exclude'][$i], ":") !== false) {
                                            // specific to QUERY_ACTION:API.  e.g:  find:user
                                            if ($config['exclude'][$i] == $query) {
                                                $pass = true;
                                                $i    = $max;
                                            }

                                        } else {
                                            // allows any QUERY_ACTION belong to the API.  e.g:  find:user, get:user and etc...
                                            if ($config['exclude'][$i] == substr($query, stripos($query, ":") + 1)) {
                                                $pass = true;
                                                $i    = $max;
                                            }
                                        }
                                        break;
                                }
                            }
                        }

                    } else {
                        $pass = true;
                    }

                    if ($pass) {
                        $send_jwt_to_client = true;

                        if (!is_object(flayer::get(${"fq_" . $k}))) {
                            // if (strpos($k, ":") !== false) {
                            //     list($k) = explode(":", $k);
                            // }

                            // $_k = current(array_keys($v));

                            // if ($_k == "custom" || $_k == "exec") {
                            //     $_path = explode("/", $k);
                            //     if (is_array($_path)) {
                            //         $class_method = end($_path);
                            //         array_pop($_path);

                            //         if (is_array($_path)) {
                            //             $class = end($_path);
                            //             $k     = join("/", $_path);
                            //         }
                            //     }
                            // }

                            // unset($_k);

                            include_once $this->path . $v->namespace . ".php";
                            $instance = flayer::bind((new $class), $instance_name);
                            $instance->construct_fql();
                        } else {
                            $instance = flayer::get($instance);
                        }
                        
                        $instance->query = $this->query[$k];
                        // unset($this->query[$k][$query_action]);

                        if (strpos($instance->query->id, ":") !== false && strpos($instance->query->id, ".id") !== false) {
                            list($query_action, $query_ask)  = explode(":", $instance->query->id);
                            list($query_func, $query_col_id) = explode(".", $query_ask);
                            $instance->query->id             = $this->query[$query_func][$query_action]->id;

                            unset($query_action);
                            unset($query_ask);
                            unset($query_func);
                            unset($query_col_id);
                        }

                        if (isset($instance->query->action) && is_value($instance->query->action)) {
                            $query_action = $instance->query->action;
                        }

                        // echo $query_action.PHP_EOL;

                        if ($v->action == "schema"){
                            $data['schema'][$v->get.":".str_replace("/", "_", $v->namespace)] = $instance->$query_action();
                        }else{
                            $data[$k] = $instance->$query_action();
                        }
                        
                        // $data[$k] = [];
                        $class_method = "";
                    } else {
                        // $data[$k][$query_action] = "Error 401 Unauthorized";
                        $query_jwt_has_failed  = true;
                        $data[$k] = [];
                    }
                }

                $request_action = "";
            }
            
            $class         = null;
            $instance      = null;
            $instance_name = null;
            $query         = null;
            $query_action  = null;

            if ($this->http->is_jwt_auth) {
                if (!$send_jwt_to_client) {
                    if (!$this->http->is_jwt_auth_success) {
                        $this->http->http_error(401);
                    }

                    echo json_encode($data);
                } else {
                    if ($query_jwt_has_failed) {
                        $this->http->http_error(401);
                    }
                    echo json_encode([["token" => flayer::jwt()->generate_token()], $data]);
                }
            } else {
                header("Content-type: application/json; charset=utf-8");
                echo json_encode($data);
            }

            $query = null;
        } else {
            header("Content-type: application/json; charset=utf-8");
            echo json_encode(["date_time" => $_SERVER['REQUEST_TIME'], "status" => "Not JSON data"]);
        }
    }

    public function set_path($path)
    {
        $this->path = realpath($this->base_path . DIRECTORY_SEPARATOR . $path) . DIRECTORY_SEPARATOR;

        if ($this->path == false) {
            die("Path -- $path not found in /app/ directory");
        }
    }

    /**
     * Request parser
     *
     * @param mixed $object
     */
    private function analyze($object)
    {
        if (is_array($object) && count($object) > 0) {
            foreach ($object as $obj_k => $obj_v) {
                $vars   = get_object_vars($obj_v);
                $action = "";
                $key    = "";

                foreach ($vars as $k => $v) {
                    if(in_array($k, $this->action)){
                        if ($k == "schema"){
                            $action = $k;
                            $key    = (array) $v;

                            foreach ($key as $schema_k => $schema_v) {
                                $_v = $schema_v;
                                $schema_v = str_replace(":", "_", str_replace("/", "_", $schema_v));
                                $this->init_query("${schema_k}_$schema_v", $k);
                                $this->query["$k:${schema_k}_$schema_v"]->get = $schema_k;
                                $this->query["$k:${schema_k}_$schema_v"]->namespace = $_v;

                                unset($_v);
                            }
                        }else{
                            $action = $k;
                            $key    = str_replace(":", "_", str_replace("/", "_", $v));
                            $exec = "";

                            if (strpos($v, ":") !== false) {
                                $_ = explode(":", $v);
                                $exec = array_pop($_);
                                $v = implode("", $_);

                                unset($_);
                            }

                            if (isset($vars['action']) && is_value($vars['action'])) {
                                $key .= "_".$vars['action'];
                            }

                            $this->init_query($key, $action);
                            $this->query["$action:$key"]->namespace = $v;

                            if(is_value($exec)){
                                $this->query["$action:$key"]->action = $exec;
                                unset($exec);
                            }

                            foreach ($vars as $_k => $_v) {
                                if(!in_array($_k, $this->action)){

                                    if($_k == "exec"){
                                        $this->query["$action:$key"]->action = $_v;
                                    }else{
                                        $this->query["$action:$key"]->{$_k} = $_v;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Initialize QUERY structure.
     *
     * @param string $key Table name
     * @param string $action Action function name.  e.g:  get, find
     */
    private function init_query($key, $action)
    {
        $this->query["$action:$key"]         = (object) null;
        $this->query["$action:$key"]->action = $action;
        $this->query["$action:$key"]->data   = null;
        $this->query["$action:$key"]->read   = (object) ["from" => 0, "to" => 0];
        $this->query["$action:$key"]->id     = null;
        $this->query["$action:$key"]->method = null;
        $this->query["$action:$key"]->namespace  = null;
        $this->query["$action:$key"]->search = null;
        $this->query["$action:$key"]->select = null;
        $this->query["$action:$key"]->sort   = null;
    }
}
