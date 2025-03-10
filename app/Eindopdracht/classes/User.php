<?php

    class Account {

        // Properties
        private $id;
        private $username;

        // Methods
        public function set_id($id) {
            $this->id = $id;
        }

        public function get_id() {
            return $this->id;
        }

        function set_username ($username) {
            $this->username = $username;
        }

        function get_username () {
            return $this->username;
        }
    }
?>