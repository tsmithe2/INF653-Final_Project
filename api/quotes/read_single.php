<?php 
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    require("../../config/Database.php");
    require("../../models/Quote.php");

    $database = new Database();
    $db = $database->connect();
    $quote = new Quote($db);
    $quote->id = isset($_GET["id"]) ? $_GET["id"] : die();
    $quotes = file_get_contents("http://localhost/final_project/api/quotes/"); //quotes
    $quotes_obj = json_decode($quotes, true);

    //if id is invalid then show error message
    if ($_GET["id"] > count($quotes_obj) || $_GET["id"] <= 0)
    {
        echo json_encode(array("message" => "No Quote Found"));
        return false;
    }

    $quote->read_single();
    $quote_arr = array("id" => $quote->id, "quote" => $quote->quote, "author" => $quote->author, "category" => $quote->category);
    print_r(json_encode($quote_arr));
?>