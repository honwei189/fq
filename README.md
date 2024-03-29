# Fq
Fast Query (Simple web query engine) is designed for PHP (plan to develop in others language).

This is alternate of RESTful API, to reduce IO connections and provide more faster query results to improve APP fast getting results or perform data actions performances and to reduce mobile data usages.

Easy to use for Web API, and also to query data from API server with multiple requests per query (1 HTTP POST connection).

Example:  Query for `User Profile`, `User History` and `All signed-on users list` at once (e.g:  open one connection -- http://API, and send 3 requests with specific JSON format), server shall return `User Profile`, `User History` and `All signed-on users list`.  This is not long persistent / long pulling / websocket

  - Easy to use
  - JSON based data structure
  - Fast to build CRUD API
  - Multiple data query in one HTTP request
  - Save IO
  - Reduce mobile data usages
  - JWT authentication (Enable / disable in config file) and allows for specific API not to use JWT

## Example (Client)

```php
$request[]["user/user"] = [
    "find" => [
        "where"  => "status != 'I'",
        "select" => "id, name, gender, age, last_login, status",
        "read"  => [
            "from" => 0,
            "to"   => 20,
        ],
    ],
    "get" => [
        "select" => "id, name, gender, age, last_login, status",
        "id" => 1
    ],
];

$payload = json_encode($request);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://localhost:8000/index.php");
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, 0); // DO NOT RETURN HTTP HEADERS
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // RETURN THE CONTENTS OF THE CALL
// curl_setopt($ch, CURLOPT_VERBOSE, true);

curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

$data = curl_exec($ch);

$error = curl_error($ch);
if (!empty($error)) {
    echo $error;
    exit;
}

curl_close($ch);

// echo $data;

print_r(json_decode($data));
```

### Example (Server)
```php
index.php // APPLICATION_PATH/public/index.php

<?php
    require __DIR__ . '/../vendor/autoload.php';

    $app = new honwei189\Flayer;
    honwei189\Flayer\Config::load();

    $app->bind("honwei189\\FDO\\FDO", "FDO");
    $app->bind("honwei189\\FQ\\Server", "Fq");

    $dbh = $app->FDO()->connect(honwei189\config::get("database", "mysql"));
    
    $app->Fq()->set_path("api"); // APPLICATION_PATH/app/api
    $app->Fq()->bootstrap();
?>

user/user.php // APPLICATION_PATH/app/api/user/user.php

<?php
class user
{
    public function __construct()
    {
        // Define schema for CRUD / query purpose

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

```

### Installation

To use FQ, you are requires to install [`Flayer`](https://github.com/honwei189/flayer.git) and [`FDO`](https://github.com/honwei189/fdo.git)

```sh
$ composer require honwei189/FDO
```
or
```sh
$ git clone https://github.com/honwei189/flayer.git
$ git clone https://github.com/honwei189/fdo.git
$ git clone https://github.com/honwei189/fq.git
```

### Documentation

Coming soon...
