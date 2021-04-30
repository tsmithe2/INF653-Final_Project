<?php 
    class Quote 
    {
        private $conn;
        private $table = "quotes";

        public $id;
        public $quote;
        public $author;
        public $category;
        public $authorId;
        public $categoryId;

        public function __construct($db) 
        {
            $this->conn = $db;
        }

        public function read() 
        {
            $query = "SELECT * FROM " . $this->table . " WHERE ";
            
            if (isset($_GET["authorId"]) && $_GET["authorId"] != 0)
            {
                $query .= "authorId = " . $_GET["authorId"] . " AND ";
            }

            if (isset($_GET["categoryId"]) && $_GET["categoryId"] != 0)
            {
                $query .= "categoryId = " . $_GET["categoryId"] . " AND ";
            }

            $query .= "true";
            
            if (isset($_GET["limit"]))
            {
                $query .= " LIMIT " . $_GET["limit"];
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        public function read_single() 
        {
            $query = "SELECT * FROM " . $this->table . " WHERE id = " . $_GET["id"];
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->quote = $row['quote'];
            $authors = file_get_contents("https://inf653-final-project.herokuapp.com/api/authors/");
            $authors_obj = json_decode($authors, true);

            for ($i = 0; $i < count($authors_obj); $i++)
            {
                if ($row["authorId"] == $authors_obj[$i]["id"])
                {
                    $this->author = $authors_obj[$i]["author"];
                    break;
                }
            }

            $categories = file_get_contents("https://inf653-final-project.herokuapp.com/api/categories/");
            $categories_obj = json_decode($categories, true);

            for ($i = 0; $i < count($categories_obj); $i++)
            {
                if ($row["categoryId"] == $categories_obj[$i]["id"])
                {
                    $this->category = $categories_obj[$i]["category"];
                    break;
                }
            }
        }

        public function create() 
        {
            $query = "INSERT INTO " . $this->table . " SET quote = :quote, authorId = :authorId, categoryId = :categoryId";
            $stmt = $this->conn->prepare($query);

            $this->quote = htmlspecialchars(strip_tags($this->quote));
            $this->authorId = htmlspecialchars(strip_tags($this->authorId));
            $this->categoryId = htmlspecialchars(strip_tags($this->categoryId));

            $authors = file_get_contents("https://inf653-final-project.herokuapp.com/api/authors/");
            $authors_obj = json_decode($authors, true);
            $categories = file_get_contents("https://inf653-final-project.herokuapp.com/api/categories/");
            $categories_obj = json_decode($categories, true);

            if (empty($this->quote) || empty($this->authorId) || empty($this->categoryId) || 
            $this->authorId > count($authors_obj) || $this->categoryId > count($categories_obj) ||
            $this->authorId <= 0 || $this->categoryId <= 0 || 1 === preg_match('~[0-9]~', $this->quote))
            {
              return false;
            }

            $stmt->bindParam(":quote", $this->quote);
            $stmt->bindParam(":authorId", $this->authorId);
            $stmt->bindParam(":categoryId", $this->categoryId);

            if($stmt->execute()) 
            {
              return true;
            }

            printf("Error: %s.\n", $stmt->error);
            return false;
        }

        public function update() 
        {
            $query = "UPDATE " . $this->table . " SET quote = :quote, authorId = :authorId, categoryId = :categoryId WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));
            $this->quote = htmlspecialchars(strip_tags($this->quote));
            $this->authorId = htmlspecialchars(strip_tags($this->authorId));
            $this->categoryId = htmlspecialchars(strip_tags($this->categoryId));

            $quotes = file_get_contents("https://inf653-final-project.herokuapp.com/api/quotes/");
            $quotes_obj = json_decode($quotes, true);

            $authors = file_get_contents("https://inf653-final-project.herokuapp.com/api/authors/");
            $authors_obj = json_decode($authors, true);

            $categories = file_get_contents("https://inf653-final-project.herokuapp.com/api/categories/");
            $categories_obj = json_decode($categories, true);

            if ($this->id > count($quotes_obj) || $this->authorId > count($authors_obj) || 
            $this->categoryId > count($categories_obj) || $this->id <= 0 || $this->authorId <= 0 ||
            $this->categoryId <= 0 || empty($this->id) || empty($this->authorId) || empty($this->categoryId) ||
            1 === preg_match('~[0-9]~', $this->quote))
            {
                return false;
            }

            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":quote", $this->quote);
            $stmt->bindParam(":authorId", $this->authorId);
            $stmt->bindParam(":categoryId", $this->categoryId);

            if($stmt->execute()) 
            {
                return true;
            }

            printf("Error: %s.\n", $stmt->error);
            return false;
        }

        public function delete() 
        {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));

            $quotes = file_get_contents("https://inf653-final-project.herokuapp.com/api/quotes/");
            $quotes_obj = json_decode($quotes, true);

            if ($this->id > count($quotes_obj) || $this->id <= 0)
            {
                return false;
            }

            $stmt->bindParam(":id", $this->id);

            if($stmt->execute()) 
            {
                //change the auto increment to the number of items in the databse.
                $query = "ALTER TABLE " . $this->table . " AUTO_INCREMENT = " . count($quotes_obj);
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                return true;
            }

            printf("Error: %s.\n", $stmt->error);
            return false;
        }
    }
?>