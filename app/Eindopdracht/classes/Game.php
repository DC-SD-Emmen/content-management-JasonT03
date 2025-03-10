<?php

    class Game {
            
        // Properties

        private $id;
        private $title;
        private $genre;
        private $platform;
        private $developer;
        private $release_year;
        private $rating;
        private $description;
        private $image;

        // Methods

        public function set_id($id) {
            $this->id = $id;
        }
    
        public function get_id() {
            return $this->id;
        }

        function set_title ($title) {
            $this->title = $title;
        }

        function get_title () {
            return $this->title;
        }

        function set_genre ($genre) {
            $this->genre = $genre;
        }

        function get_genre () {
            return $this->genre;
        }

        function set_platform ($platform) {
            $this->platform = $platform;
        }

        function get_platform () {
            return $this->platform;
        }

        function set_developer ($developer) {
            $this->developer = $developer;
        }

        function get_developer () {
            return $this->developer;
        }

        function set_release_year ($release_year) {
            $this->release_year = $release_year;
        }

        function get_release_year () {
            return $this->release_year;
        }

        function set_rating ($rating) {
            $this->rating = $rating;
        }

        function get_rating () {
            return $this->rating;
        }  

        function set_description ($description) {
            $this->description = $description;
        }

        function get_description () {
            return $this->description;
        }

        function set_image ($image) {
            $this->image = $image;
        }

        function get_image () {
            return $this->image;
        }

    }

?>