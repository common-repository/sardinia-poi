<?php

if (!class_exists('SardiniaPoi_JsonClass')) {

    class SardiniaPoi_JsonClass {

        public $json_data = [];
        const POI_PATH = '../content/dataset/sardiniapoi-rsa-data.json';

        public function __construct() {
            $json_string = file_get_contents(plugin_dir_path(__FILE__) . self::POI_PATH);
        
            if ($json_string !== false) {
                $dataNoDuplicates = array_map("unserialize", array_unique(array_map("serialize", json_decode($json_string, true))));
                $this->json_data = $dataNoDuplicates; 
            }
        }
        
        public function sardiniapoi_filterJson($filters) {
            $filtered_data = array();
            if (array_key_exists('limit', $filters)) {
                $limit = $filters['limit'];
                unset($filters['limit']);
            }
            foreach ($this->json_data as $item) {
                $match = true;
                $lowercase_item = array_change_key_case($item, CASE_LOWER);
                
                foreach ($filters as $key => $value) {
                    $lowercase_key = strtolower($key);
                    $lowercase_value = strtolower($value);
                    
                    if (!isset($lowercase_item[$lowercase_key]) || strtolower($lowercase_item[$lowercase_key]) !== $lowercase_value || $lowercase_item['macro tipologia']=='City e paesi') {
                        $match = false;
                        break;
                    }
                }
                
                if ($match) {
                    $filtered_data[] = $item;
                }
            }
            
            return array_slice($filtered_data, 0, $limit);
        }

    }
}