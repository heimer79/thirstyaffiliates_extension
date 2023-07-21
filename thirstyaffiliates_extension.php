<?php
/*
Plugin Name: ThirstyAffiliates Extended
Description: Extiende la funcionalidad del plugin ThirstyAffiliates para manejar cadenas de consulta especiales en las URL de redirección.
Author: Heimer Martinez
Author URI: https://github.com/heimer79/thirstyaffiliates_extension
Version: 1.0.2
Text Domain: thirstyaffiliates-pro
*/

// Asegurémonos de que no estamos exponiendo ninguna información si se llama directamente
if (!defined('ABSPATH')) {
    exit;
}

// Verificamos si ThirstyAffiliates pro está activo
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

$plugin_slug = 'thirstyaffiliates/thirstyaffiliates.php';

// Comprobar si el plugin está activo
if (is_plugin_active($plugin_slug)) {

    // Definir una función para modificar la URL de redirección
    function ta_extended_filter_redirect_url($redirect_url, $thirstylink, $query_string = '') {

        // Si $query_string está vacío, tomar el valor de $_SERVER['QUERY_STRING']
        if (!$query_string && isset($_SERVER['QUERY_STRING']))
            $query_string = sanitize_text_field(wp_unslash($_SERVER['QUERY_STRING']));

        // Si $query_string sigue estando vacío o $thirstylink no tiene 'pass_query_str', retornar $redirect_url sin cambios
        if (!$query_string || !$thirstylink->is('pass_query_str'))
            return $redirect_url;

        // Si la URL de redirección tiene más de un parámetro
        if (substr_count($redirect_url, '&') > 1) {
            // Reemplazar todas las apariciones de '=&' por '='
            $redirect_url = str_replace('=&', '=', $redirect_url);
            // Retornar la URL de redirección modificada
            return $redirect_url;
        } else {
            // Parsear la URL de redirección
            $parsed_url = parse_url($redirect_url);

            // Extraer las partes de la URL
            $scheme = $parsed_url['scheme'];
            $host = $parsed_url['host'];
            $path = $parsed_url['path'];
            $query = $parsed_url['query'];

            // Descomponer la cadena de consulta en sus componentes
            $query_components = explode('=', $query);

            // Extraer el nombre y el valor del primer parámetro
            $param_name = $query_components[0];
            $param_value = $query_components[1];

            // Componer la URL base sin los parámetros
            $redirect_url_simple = $scheme . '://' . $host . $path;

            // Determinar el conector adecuado para los parámetros
            $connector  = (strpos($redirect_url_simple, '?') === false) ? '?' : '';

            // Si $query_string comienza con '?', eliminarlo
            if (strpos($query, '?') !== false) {
                $query_string = ltrim($query_string, '?');
                return  $redirect_url_simple . $connector . $param_name . '=' . $query_string;
            } else {
                // Añadir $query_string a la URL de redirección
                return  $redirect_url_simple . $connector . $param_name . '=' . '&' . $query_string;
            }
        }
    }

    // Añadir la función como un filtro para modificar la URL de redirección
    add_filter('ta_filter_redirect_url', 'ta_extended_filter_redirect_url', 20, 3);
} else {
    // Si el plugin no está activo, mostrar una notificación en el panel de administración
    add_action('admin_notices', 'my_admin_notice');
    function my_admin_notice() {
?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('ThirstyAffiliates Pro plugin is required!', 'thirstyaffiliates-pro'); ?></p>
        </div>
<?php
    }
}
