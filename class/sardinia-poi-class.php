<?php 

if (!class_exists('sardiniaPoi_mainClass')) {

    class sardiniaPoi_mainClass {

        public $poi;
        public $filter_list = ["COMUNE","PROVINCIA","REGIONE","NOME","MACRO TIPOLOGIA","TIPOLOGIA","SCHEDA"];

        const SARDINIAPOIHEADER = 'sardinia_poi_title_area';
        const SARDINIAPOITEMPLATE = 'sardinia_poi_template';

        const SARDINIAPOI_DEFAULT_TEMPLATE = '{TIPOLOGIA} - {NOME}: {google_link}';

        public function __construct() {
            $this->poi = new sardiniaPoi_JsonClass();
            $notice = new sardinia_poi_notice_class();
            add_action('admin_menu', [$this,'sardiniapoi_configuration']);
            add_action('admin_init', [$this,'sardiniapoi_start_option']);
            add_shortcode('sardinia_poi_city', [$this,'sardiniapoi_filter']);
        }


        public function sardiniapoi_filter ($atts) {
            
            $risultato = $this->poi->sardiniapoi_filterJson($atts); 
            
            return $this->sardiniapoi_renderResponse($risultato);
        }

        private function sardiniapoi_renderResponse($data) {
            if(!$data) return '';
            $text = "<h3>".get_option(SELF::SARDINIAPOIHEADER)."</h3>";
            $text .= '<ul>';
            foreach($data as $poi) {
                $text .= '<li>'.str_replace(['{TIPOLOGIA}', '{NOME}','{google_link}'],[$poi['TIPOLOGIA'],$poi['NOME'],$this->sardiniapoi_generateGoogleMapsLink($poi['DO_Y'],$poi['DO_X'])],$this->getSardiniaPoiTemplate()).'</li>';
            }
            $text .= '</ul>';

            return $text;
        }

        private function getSardiniaPoiTemplate() {
            return get_option(SELF::SARDINIAPOITEMPLATE) ? get_option(SELF::SARDINIAPOITEMPLATE) : SELF::SARDINIAPOI_DEFAULT_TEMPLATE;
        }

        public function sardiniapoi_generateGoogleMapsLink($latitude, $longitude) {
            return '<a href="https://www.google.com/maps?q='.$latitude.','.$longitude.'" target="_blank">'.__('address','sardinia-poi').'</a>';
        }

        public function sardiniapoi_configuration() {
            add_menu_page(
                __('Sardinia POI','sardinia-poi'),
                __('Sardinia POI','sardinia-poi'), 
                'manage_options',
                'sardinia_poi_option_page',
                [$this,'sardiniapoi_configuration_render'],
                'dashicons-admin-post',
                99 
            );
        }
        
        public function sardiniapoi_configuration_render() {
            ?>
            <div class="wrap">
                <form method="post" action="options.php">
                    <?php settings_fields('sardinia_poi_option'); ?>
                    <?php do_settings_sections('sardinia_poi_option_page'); ?>
                    <?php submit_button('Salva Impostazioni'); ?>
                </form>
            </div>
            <?php
        }

        public function sardiniapoi_start_option() {
            $fields = array(
                array(
                    'id' => SELF::SARDINIAPOIHEADER,
                    'label' => __('Title area','sardinia-poi'),
                    'callback' => [$this,'sardiniapoi_render_field_standard'],
                    'section' => 'sardinia_poi_option_section',
                    'page' => 'sardinia_poi_option_page'
                ),
                array(
                    'id' => SELF::SARDINIAPOITEMPLATE,
                    'label' => __('Template single poi','sardinia-poi'),
                    'callback' => [$this,'sardiniapoi_render_field_textarea'],
                    'section' => 'sardinia_poi_option_section',
                    'page' => 'sardinia_poi_option_page'
                )
            );
        
            register_setting('sardinia_poi_option', SELF::SARDINIAPOIHEADER);
            register_setting('sardinia_poi_option', SELF::SARDINIAPOITEMPLATE);
        
            add_settings_section(
                'sardinia_poi_option_section', 
                __('Plugin settings','sardinia-poi'),
                [$this,'sardiniapoi_option_section_render'],
                'sardinia_poi_option_page'
            );
        
            foreach ($fields as $field) {
                add_settings_field(
                    $field['id'],
                    $field['label'],
                    $field['callback'],
                    $field['page'],
                    $field['section'],
                    $field
                );
            }
        }
        
        public function sardiniapoi_render_field_standard($args) {
            $value = get_option($args['id']);
            echo "<input type='text' name='".esc_attr($args['id'])."' value='".esc_attr($value)."' />";

        }
        
        public function sardiniapoi_render_field_textarea($args) {
            $value = get_option($args['id']);
            $default_content = '{TIPOLOGIA} - {NOME}: {google_link}';
            ob_start();
            wp_editor($value ? $value : $default_content, $args['id'], [
                'textarea_name' => esc_attr($args['id']),
                'textarea_rows' => 10 
            ]);
            echo ob_get_clean();
        }
        

        public function sardiniapoi_option_section_render() {
            echo esc_html__("Enter your plugin settings here.",'sardinia-poi');
        }
    
    }
}