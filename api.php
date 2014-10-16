<? 
/**
 * @author Marius Žilėnas <mzilenas@gmail.com>
 * @copyright 2013 Marius Žilėnas
 */
include 'includes.php';

/**
 * Start buffering.
 */
ob_start();
DB::connect(); 
session_start();
ob_end_clean(); //Must have clean output for api

$api = TRUE;

/**
 * Collect api output.
 */
ob_start();
Req::process($api);
ob_flush();

