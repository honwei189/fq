# fq
Fast Query Language (Simple Web Query Language).  This is alternative of RESTful API, to reduce IO connections and provide more faster query results, easy to use for Web API, and also to query data from API server with multiple requests per query (1 HTTP POST connection).  Example:  Query for User Profile, User History and All signed-on users list at once (e.g:  open one connection -- http://API, and send 3 requests with requested specific JSON format), server shall return User Profile, User History and All signed-on users.  This is not long persistent / long pulling / websocket

  - Easy to use
  - JSON based data structure
  - Fast to build CRUD API
  - Multiple data query in one HTTP request
  - Save IO

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

### Installation

To use FDO, you are requires to install [`flayer`](https://github.com/honwei189/flayer.git)

```sh
$ composer require honwei189/fdo
```
or
```sh
$ git clone https://github.com/honwei189/flayer.git
$ git clone https://github.com/honwei189/fdo.git
$ git clone https://github.com/honwei189/fq.git
```
