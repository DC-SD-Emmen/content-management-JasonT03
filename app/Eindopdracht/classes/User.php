<?php

    class User {

        // Properties
        private $id;
        private $username;
        private $email;

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

        function set_email ($email) {
            $this->email = $email;
        }

        function get_email () {
            return $this->email;
        }
    }
?>