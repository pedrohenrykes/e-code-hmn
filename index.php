<?php

// ini_set ( 'display_errors', 1 );
// ini_set ( 'display_startup_erros', 1 );
// error_reporting ( E_ALL );

require_once "init.php";

$theme  = $ini[ "general" ][ "theme" ];
$class  = isset( $_REQUEST[ "class" ] ) ? $_REQUEST[ "class" ] : "";
$public = in_array( $class, $ini[ "permission" ][ "public_classes" ] );

new TSession;

if ( TSession::getValue( "logged" ) ) {
    $content     = file_get_contents( "app/templates/{$theme}/layout.html" );
    $menu_string = SideMenuCreate::createUserMenu();
    $content     = str_replace( "{MENU}", $menu_string, $content );
} else {
    $content = file_get_contents( "app/templates/{$theme}/login.html" );
}

$smallimage = new TImage( "app/images/default/logo_small.png" );
$largeimage = new TImage( "app/images/default/logo_large.png" );

$content  = str_replace( "{LIBRARIES}", file_get_contents( "app/templates/{$theme}/libraries.html" ), $content );
$content  = str_replace( "{class}", $class, $content );
$content  = str_replace( "{template}", $theme, $content );
$content  = str_replace( "{username}", TSession::getValue( "username" ), $content );
$content  = str_replace( "{frontpage}", TSession::getValue( "frontpage" ), $content );
$content  = str_replace( "{query_string}", $_SERVER[ "QUERY_STRING" ], $content );
$content  = str_replace( "{smallLogo}", $smallimage, $content );
$content  = str_replace( "{largeLogo}", $largeimage, $content );
$content  = str_replace( "{systemname}", "Kiron Saúde", $content );
$content  = str_replace( "{systemversion}", "Alpha", $content );
$content  = str_replace( "{systemowner}", "e-Code", $content );
$content  = str_replace( "{pagetitle}", "Kiron Saúde", $content );
$content  = str_replace( "{pagefavicon}", "app/images/default/favicon.png", $content );
$content  = str_replace( "{homepage}", "#", $content );
$css      = TPage::getLoadedCSS();
$js       = TPage::getLoadedJS();

$content  = str_replace( "{HEAD}", $css.$js, $content );

echo $content;

if ( TSession::getValue( "logged" ) OR $public ) {
    if ( !empty( $class ) ) {
        $method = isset( $_REQUEST[ "method" ] ) ? $_REQUEST[ "method" ] : NULL;
        AdiantiCoreApplication::loadPage( $class, $method, $_REQUEST );
    }
} else {
    AdiantiCoreApplication::loadPage( "LoginForm", "", $_REQUEST );
}
