<?php

    class DB {

        private $host;
        private $user;
        private $password;
        private $database;
        private $conn;

        public function __construct($host, $user, $password, $database) {
            $this->host = $host;
            $this->user = $user;
            $this->password = $password;
            $this->database = $database;
        }
        public function connect() {
            $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        }

        #promjeniti
        public function req_query($query) {
            $this->conn->query($query);
            return True;
            
        }

        public function query($query) {
            $result = $this->conn->query($query);
            $results = [];
            error_log("**************");
            error_log($query." ___>>>" .print_r($result,1));
            error_log("**************");
            while ($row = $result->fetch_object()) {
                $results[] = $row;
            }

            return $results;
        }

        public function update($query) {
            $result = $this->conn->query($query);
            return $result;
        }

        //TODO
        public function betterQuery($query, $params) {
            if ($params == null) {
                return query($query);
            }
            $result = $this->conn->query($query);
            $results = [];
            
            while ($row = $result->fetch_object()) {
                $results[] = $row;
            }
            return $results;
        }

        public function cleanup($string) {
            return $this->conn->real_escape_string($string);
        }

        public function hash($string) {
            return md5($str);
        }
    }
