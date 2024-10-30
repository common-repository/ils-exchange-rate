<?php
/*
Plugin Name: ILS Exchange Rate
Plugin URI: http://livne-cpa.co.il
Description: Display the Israeli exchange rate on a WordPress page or post using nothing but a shortcode.
Author: OK Digital LTD.
Version: 0.1

*/

/**
 * Register style sheet.
 */
add_action( 'wp_enqueue_scripts', 'ILSES_register_style' );
function ILSES_register_style() {
    wp_register_style( 'ILSES_table_style', plugin_dir_url( __FILE__ ) . 'style.css' );
    wp_enqueue_style( 'ILSES_table_style' );
}


/**
 * echos shortcode
 */
function ILSES_Exchange_Shortcode() {

    $curl = wp_remote_get( 'https://www.boi.org.il/currency.xml',
        array(
        'user-agent'  => 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0'
        )
    );

    $table = ILSES_ParseExchangeRate( wp_remote_retrieve_body($curl) );
    $info = '<div id="ILSExchangeRate">'.$table.'</div>';
    return $info;
}
add_shortcode( 'ILSES', 'ILSES_Exchange_Shortcode' );

/**
 * @param $data
 * @return string
 */
function ILSES_ParseExchangeRate( $data ){
    $xml = simplexml_load_string( $data ) or die( "Error: Cannot create object" );
    $last_update = $xml->LAST_UPDATE;

    $html = "<div class='ILS_inner'>";
    $html .= "עודכן לאחרונה ב-<span>".$last_update."</span>";
    $html .= "<table class='table table-hover'><thead class='thead-dark'><tr><th>קוד</th><th>סוג מטבע</th><th>מדינה</th><th>שער יציג</th></tr></thead><tbody>";

    foreach( $xml->CURRENCY as $each ){
        $CURRENCYCODE = $each->CURRENCYCODE;
        $NAME = $each->NAME;
        $COUNTRY = $each->COUNTRY;
        $RATE = $each->RATE;
        $html .= "<tr><td>".$CURRENCYCODE."</td><td>".$NAME."</td><td>".$COUNTRY."</td><td>".$RATE."</td></tr>";
    }
    $html .= "</tbody></table></div>";
    return $html;
}
?>

