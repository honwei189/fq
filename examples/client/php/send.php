<?php
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

// print_r($a);

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
