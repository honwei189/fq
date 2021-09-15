<?php
class lists
{
    // use honwei189\Fq\Fql;

    public function __construct()
    {
        $this->define_schema("id", "ID");
        $this->define_schema("folder", "Folder name", "tag");
        $this->define_schema("name", "Folder / file name");
        $this->define_schema("size", "Folder / file size");
        $this->define_schema("sdt", "Start date & time");
        $this->define_schema("edt", "End date & time");
        $this->define_schema("path", "Saved path");
        $this->define_schema("completion", "Completion");
        $this->define_schema("status", "Download status");
        $this->define_schema("cdt", "Record creation date & time");
        $this->define_schema("ucdt", "Record update date & time");
    }
}
