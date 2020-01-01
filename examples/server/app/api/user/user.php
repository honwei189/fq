<?php
class user
{
    use \honwei189\fq\fql;
    
    public function __construct()
    {
        $this->table = "users";
        $this->define_schema("id", "ID");
        $this->define_schema("name", "User name");
        $this->define_schema("userid", "User ID");
        $this->define_schema("gender", "Gender");
        $this->define_schema("role", "User role");
        $this->define_schema("utyp", "User type.  S = System admin, A = Admin, U = User");
        $this->define_schema("email", "Email address");
        $this->define_schema("addr", "Address");
        $this->define_schema("status", "Account status");
        $this->define_schema("crdt", "Record creation date & time");
    }

    // public function read(){
    //     // $this->define_schema("id", "ID");
    //     // $this->define_schema("name", "User name");
    //     // $this->define_schema("userid", "User ID");
    //     // $this->define_schema("gender", "Gender");
    //     // $this->define_schema("age", "Age");
    //     // $this->define_schema("email", "Email address");
    //     // $this->define_schema("address", "Address");
    //     // print_r($this);

    //     return $this->query();

    //     // return $this->db->by_id($this->query->id)->cols($this->query->select)->get();
    // }

    // public function find()
    // {
    //     return $this->query();
    // }

    public function test(){
        // print_r($this->query);
        
        return json_encode(["aaa"]);
    }
}
?>