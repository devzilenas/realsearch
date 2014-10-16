<?
set_include_path(get_include_path() . PATH_SEPARATOR . 'class');
set_include_path(get_include_path() . PATH_SEPARATOR . 'lib');

# -------------- CONFIG -------------------
include 'config.inc.php'                   ;

# -----------------------------------------
# -------------- LIB ----------------------
# -----------------------------------------
# -------------- DB -----------------------
include_once 'db/db.class.php'; 
# -------------- DBOBJS -------------------
include_once 'dbobjs/dbobj.class.php'      ;
include_once 'dbobjs/dbobj.interface.php'  ;
include_once 'dbobjs/objset.class.php'     ;
include_once 'dbobjs/objset_html.class.php';
include_once 'dbobjs/filter.class.php'     ;
include_once 'dbobjs/sql_filter.class.php' ;
include_once 'dbobjs/union_filter.class.php';
# -------------- MONEY --------------------
include_once 'money/dbobjs/exchange_rate.class.php';
include_once 'money/currency.class.php'            ;
include_once 'money/exchange_rator.class.php';
include_once 'money/monia.class.php'       ;
include_once 'money/syncer_rate.class.php' ;
include_once 'money/dbobjs/wallet.class.php';
include_once 'money/dbobjs/wallet_line.class.php';
# -------------- ITEMS LIST ---------------
include_once 'dbobjs/html/items_list.html.php';
include_once 'dbobjs/req/list.req.php'     ;
# -------------- RANKS --------------------
include_once 'dbobjs/rank/rankenstein.class.php';
# -------------- LANGUAGE ----------------- 
include_once 'lang/language.class.php';
include_once 'lang/dict/lt.inc.php';
include_once 'lang/dict/ru.inc.php';
include_once 'lang/dict/de.inc.php'; 
# -------------- LOGGER ------------------- 
include_once 'sys/logger/error.inc.php'    ;
include_once 'sys/logger/logger_html_block.class.php';
# -------------- HTML ---------------------
include_once 'html/form.class.php'         ;
include_once 'html/html.class.php'         ;
# -------------- SESSION ------------------
include_once 'sys/session.class.php'       ;
include_once 'sys/session.inc.php'         ;
# -------------- REQUEST ------------------
include_once 'sys/request.class.php'       ;
# -------------- USER ---------------------
include_once 'auth/crypt.class.php'        ;
include_once 'auth/dbobjs/user.class.php'  ;
include_once 'auth/dbobjs/user_config.class.php';
include_once 'auth/login.class.php'        ;
include_once 'auth/sys/user_session.class.php';
include_once 'auth/login_html_block.class.php';
include_once 'auth/auth.req.php';
# -------------- JSON ---------------------
include_once 'json/json.class.php';
# -------------- CALENDAR -----------------
include_once 'calendar/calendar.class.php' ;
include_once 'calendar/calendar_date.class.php';
# -----------------------------------------
# -------------- API ----------------------
# -----------------------------------------
include 'api/response.class.php';
# -----------------------------------------
# -------------- DEPLOY -------------------
# -----------------------------------------
include 'deploy/install_b.class.php';
# -----------------------------------------
# -------------- MAPS ---------------------
# -----------------------------------------
include 'maps/maps_html.class.php';

include_once 'lib.inc.php'                 ;

# -----------------------------------------
# -------------- INTERFACE ----------------
# -----------------------------------------
include 'req.interface.php';

# -----------------------------------------
# -------------- SETUP --------------------
# -----------------------------------------
$DBOBJS = array('Real', 'Field', 'Picture', 'ContactInfo', 'SearchAgent', 'SearchValue', 'LoggerAction', 'Email');
foreach($DBOBJS as $name) include 'dbobjs/'.strtolower(c2u($name)).'.class.php';
include 'dbobjs/values.class.php';

# -------------- REQUEST ------------------
include 'req.class.php'                    ;
# -------------- HTML ---------------------
include 'html_block.class.php'             ;

include 'searchable.class.php'             ;
include 'search_manager.class.php'         ;
include 'picture_manager.class.php'        ;
include 'picture_effect.class.php'         ;
include 'wallet_manager.class.php'         ;
include 'thumbnail.class.php'              ;
include 'access.class.php'                 ;
include 'runner.class.php'                 ;
include 'api_manager.class.php'            ;
include 'email_manager.class.php'          ;
