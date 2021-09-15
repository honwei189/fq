<?php
    $s = microtime(true);

    require __DIR__ . '/../vendor/autoload.php';

    use honwei189\Flayer\Config as config;

    config::set_path(__DIR__ . "/../");
    config::load();

    $app = new honwei189\Flayer\Core;
    honwei189\Flayer\Config::load();

    $app->bind("honwei189\\FDO");
    // $app->bind("honwei189\\Fw");
    $app->bind("honwei189\\FQ\\Server", "FQ");

    $dbh = $app->FDO()->connect(config::get("database", "mysql"));

    $app->FQ()->set_dir("api");
    $app->FQ()->bootstrap();

    $e = microtime(true);

    $sec     = $e - $s;
    $ms      = round((double) $sec * 1000, 2);
    $secPer  = round((double) (1 / $sec), 2);
    $sec     = round($sec, 4);
    $memPeak = round(memory_get_peak_usage() / 1024 / 1024, 4);
    $mem     = round(memory_get_usage() / 1024 / 1024, 4);

    if (php_sapi_name() == "cli") {
        echo PHP_EOL . "Generated Time : $ms ms , $sec sec " . PHP_EOL . "Memory Usage   : {$mem} mb (current), {$memPeak} mb (peak)" . PHP_EOL . PHP_EOL;
}
