<?php
/*
Plugin Name: ThirstyAffiliates Extended
Description: Extiende la funcionalidad del plugin ThirstyAffiliates para manejar cadenas de consulta especiales en las URL de redirección.
Author: Heimer Martinez
Version: 1.0
*/

// Asegurémonos de que no estamos exponiendo ninguna información si se llama directamente
if (!defined('ABSPATH')) {
    exit;
}

// Verificamos si ThirstyAffiliates está activo
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if (is_plugin_active('thirstyaffiliates/thirstyaffiliates.php')) {

    function ta_extended_filter_redirect_url($redirect_url, $thirstylink, $query_string = '') {

        if (!$query_string && isset($_SERVER['QUERY_STRING']))
            $query_string = sanitize_text_field(wp_unslash($_SERVER['QUERY_STRING']));

        if (!$query_string || !$thirstylink->is('pass_query_str'))
            return $redirect_url;



        // Parsear la URL
        $parsed_url = parse_url($redirect_url);

        // Guardar cada parte de la URL en su propia variable
        $scheme = $parsed_url['scheme'];
        $host = $parsed_url['host'];
        $path = $parsed_url['path'];
        $query = $parsed_url['query'];


        // Descomponemos la cadena de consulta en sus componentes
        $query_components = explode('=', $query);




        // Agregamos el nombre del parámetro y su valor al array $query_params
        $param_name = $query_components[0]; //nombre de la variable en la url ejemplo: lang
        $param_value = $query_components[1]; //Valor de la varieble ejemplo: es


        $redirect_url = $scheme . '://' . $host . $path;


        $connector  = (strpos($redirect_url, '?') === false) ? '?' : '';


        if (strpos($query, '?') !== false) {
            $query_string = ltrim($query_string, '?');

            return  $redirect_url . $connector . $param_name . '=' . $query_string;
        } else {
            return  $redirect_url . $connector . $param_name . '=' . '&' . $query_string;
        }
    }
    add_filter('ta_filter_redirect_url', 'ta_extended_filter_redirect_url', 20, 3);
}
