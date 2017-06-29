<?php

require __DIR__ . '/vendor/autoload.php';

$ini_array = parse_ini_file("rundeck.ini");
if (!isset($_GET["project"])) {
    echo "project must be defined";
} else {
    $project = $_GET["project"];

    $tag = null;
    if (isset($_GET["tag"])) {
        $tag = $_GET["tag"];
    }

    $client = new Rundeck\Rundeck($ini_array["rundeck_url"], $ini_array["rundeck_token"], $ini_array["rundeck_api_version"]);

    // Get all resources
    $resources = $client->project($project)->resources();

    $list = $resources["node"];
    if ($tag != null && $tag=!"All") {
        //filter by tags
        $list = array_filter($resources["node"], function ($row) use($tag) {
            return (
                    strpos($row["@attributes"]["tags"], $tag) !== false);
        });
    }

    $array_nodes = array();
    foreach ($list as $resource) {

        $value = $resource["@attributes"]["username"] . "@" . $resource["@attributes"]["hostname"] . " (" . $resource["@attributes"]["description"] . ")";
        array_push($array_nodes, array("name" => $value, "value" => $resource["@attributes"]["hostname"]));
    }

    header('Content-Type: application/json');

    $json = json_encode($array_nodes, true);
    echo $json;
}