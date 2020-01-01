<?php
class test
{
    use \honwei189\fq\fql;

    public function __construct()
    {
        $this->table = "test";
        $this->define_schema("id", "ID");
        $this->define_schema("name", "User name");
        $this->define_schema("gender", "Gender");
        $this->define_schema("age", "Age");
        $this->define_schema("status", "Account status");
        $this->define_schema("crdt", "Record creation date & time");
    }
}
