<?php 
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    require("../../config/Database.php");
    require("../../models/Quote.php");

    $database = new Database();
    $db = $database->connect();
    $quote = new Quote($db);
    $result = $quote->read();
    $num = $result->rowCount();

    if($num > 0) 
    {
        $quote_arr = array();
        while($row = $result->fetch(PDO::FETCH_ASSOC)) 
        {
            extract($row);
            $quote_item = array("id" => $id, "quote" => $quote, "author" => $authorId, "category" => $categoryId);
            array_push($quote_arr, $quote_item);
        }

        $json = json_encode($quote_arr);
        $quote_obj = json_decode($json, true);
        $authors = file_get_contents("http://localhost/final_project/api/authors/"); //authors
        $authors_obj = json_decode($authors, true);
        $categories = file_get_contents("http://localhost/final_project/api/categories/"); //categories
        $categories_obj = json_decode($categories, true);

        for ($i = 0; $i < count($quote_obj); $i++)
        {
            for ($j = 0; $j < count($authors_obj); $j++)
            {
                if($quote_obj[$i]["author"] == $authors_obj[$j]["id"])
                {
                  $quote_obj[$i]["author"] = $authors_obj[$j]["author"];
                }
            }
            for ($j = 0; $j < count($categories_obj); $j++)
            {
                if($quote_obj[$i]["category"] == $categories_obj[$j]["id"])
                {
                  $quote_obj[$i]["category"] = $categories_obj[$j]["category"];
                }
            }
        }
        echo json_encode($quote_obj);
    } 
    else 
    {
        echo json_encode(array("message" => "No Quotes Found"));
    }
?>