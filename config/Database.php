<?php 
    class Database
    {
        private $host = "y5svr1t2r5xudqeq.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
        private $db_name = "wlie207onqwptix4";
        private $username = "jd7349aias3bvg3i";
        private $password = "dhsooeult5urn5j0";
        private $conn;

        public function connect()
        {
            $this->conn = null;

            try 
            { 
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } 
            catch(PDOException $e) 
            {
                echo "Connection Error: " . $e->getMessage();
            }

            return $this->conn;
        }
    }
?>