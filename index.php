<?php

require 'includes/header.php';


$service = new service('all');

$tab_installed = $service->get_installed_appli();

$tab_uninstalled = $service->get_uninstalled_applis($tab_installed);


$applis = $service->get_all($tab_installed);

// javascripts  utilisés
$js = array(
    'jquery.min.js',
    'popper.min.js',
    'bootstrap.min.js',
    'app.min.js',
    'app.init.horizontal-fullwidth.js',
    'perfect-scrollbar.jquery.min.js',
    'sparkline.js',
    'waves.js',
    'sidebarmenu.js',
    'custom.min.js',
    'toastr.min.js',
    'toastr-init.js',
    'ssd_specific.js',
    'navigation_modal.js',
    'get_logs.js',
    'test_git.js',
    'jquery-cron-quartz.js',
    'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.js'
);

// css utilisés
$css = array(
    "style.min.css",
    "style.css",
    "toastr.css",);


$asset_css = '';
$asset_js  = '';
if ($prod)
{
    $config = [
        'pipeline'   => 'auto',
        'public_dir' => __DIR__,
        'css_dir'    => 'dist/css',
        'js_dir'     => 'dist/js'
    ];
    $assets = new \Stolz\Assets\Manager($config);
    $assets->add($css);
    $assets->add($js);
    $asset_css = $assets->css();
    $asset_js  = $assets->js();
}

/**
 * PLUS DE DEBUG BAR APRES CA !!
 */
if ($mode_debug)
{
    $debugbarrender = $debugbarRenderer->render();
}


// maintenant qu'on a toutes les variables, on appelle le bon template, en mettant les variables dedans
$template = $twig->load('index.twig');
echo $template->render(['IP'             => $_SERVER['REMOTE_ADDR'],
                        'TITRE'          => 'Gestion du serveur SSD',
                        'FREE_DISK'      => $free_disk,
                        'UPTIME'         => $uptime,
                        'SERVER_NAME'    => $server_name,
                        'APPLIS'         => $applis,
                        'JS'             => $js,
                        'CSS'            => $css,
                        'PROD'           => $prod,
                        'ASSET_CSS'      => $asset_css,
                        'ASSET_JS'       => $asset_js,
                        'DEBUGBARJS'     => $debugbar_js,
                        'DEBUGBARRENDER' => $debugbarrender,
                        'UNINSTALLED'    => $tab_uninstalled
                       ]);


