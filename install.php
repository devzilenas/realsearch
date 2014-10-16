<?
include 'includes.php'           ;
include 'auth/user.install.php'  ;
include 'money/monia.install.php';

/**
 * Class for installation logic.
 *
 * @version 0.0.2
 */
class Install extends InstallB {

	private static function make_temporary_directory() {
		$dname = Config::DIR_TMP;
		if(!file_exists($dname)) {
			mkdir($dname);
		}
	}

	private static function make_emails_directory() {
		$dname = EmailManager::edir();
		if(!file_exists($dname)) {
			mkdir($dname);
		}
	}

	private static function make_runner_directory() {
		$dname = Runner::idir();
		if(!file_exists($dname)) {
			mkdir($dname);
		}
	}

	private static function make_directories() {
		self::make_temporary_directory();
		self::make_emails_directory();
		self::make_runner_directory();
	}

	private static function make_cornered_thumbnail() {
		$tsrc = PictureManager::thumbnail_cornered();
		if(!file_exists($tsrc)) {
			$template = PictureManager::image_path(PictureManager::thumbnail_default_name());
			$im = imagecreatefrompng($template);

			/** create thumbnail cornered */
			PictureEffect::round_corners($im); 
			imagepng($im, $tsrc);

			imagedestroy($im);
		}
	}

	/**
	 * Create images.
	 *
	 */
	public static function createImages() {
		self::make_cornered_thumbnail();
	}

	/**
	 * Creates tables.
	 * 
	 * @return void
	 */
	public static function createTables() {
		/** App files */
		self::make_directories();
		self::createImages();

		/** App tables */
		self::createTableReals();
		self::createTableValueNumeric();
		self::createTableValueBoolean();
		self::createTableValueText();

		self::createTableEmails();

		self::createTablePictures();
		self::createTableContactInfos();

		/** install user */
		InstallUser::createTables();

		/** Install monia */
		InstallMonia::createTables();

		/** Install search agents */
		self::createTableSearchAgents();
		self::createTableSearchValues();

		/** Install action logger */
		self::createTableLoggerActions();

	}

	/**
	 * Create Emails table.
	 *
	 * @return void
	 */
	private static function createTableEmails() {
		$table = Email::tableName();
		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id      INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				subject VARCHAR(255),
				message TEXT        ,
				is_html TINYINT(1)  ,
				is_sent TINYINT(1)  ,
				to_     VARCHAR(255)
			)")
		or self::dieTNC($table);
	}

	/**
	 * Create logger action table.
	 */
	private static function createTableLoggerActions() {
		$table = LoggerAction::tableName();

		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id          INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name        VARCHAR(255),
				ip          VARCHAR(255),
				user_id     INTEGER,
				on_         DATETIME,
				attached_to VARCHAR(255),
				attached_id INTEGER)") 
		or self::dieTNC($table);

	}
	/**
	 * Create ValueNumeric table.
	 */
	private static function createTableValueNumeric() { 
		$table = ValueNumeric::tableName();
		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id    INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				oid   INTEGER,
				name  VARCHAR(255),
				value DECIMAL(12,3))")
			or self::dieTNC($table);
	}

	/**
	 * Create ValueBoolean table.
	 */
	private static function createTableValueBoolean() {
		$table = ValueBoolean::tableName();
		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id    INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				oid   INTEGER,
				name  VARCHAR(255),
				value INTEGER)")
		or self::dieTNC($table);
	}

	/**
	 * Create ValueText table
	 */
	private static function createTableValueText() {
		$table = ValueText::tableName();
		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id    INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				oid   INTEGER,
				name  VARCHAR(255),
				value VARCHAR(255))")
		or self::dieTNC($table);
	}

	/**
	 * Create pictures table.
	 */
	private static function createTablePictures() {
		$table = Picture::tableName();
		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id       INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id  INTEGER,
				my_picture_id VARCHAR(255),
				size     INTEGER,
				type     VARCHAR(255),
				name     VARCHAR(255),
				tmp_name VARCHAR(255),
				attached_to VARCHAR(255),
				attached_id INTEGER,
				rank     INTEGER,
				caption  VARCHAR(255))")
	   	or self::dieTNC($table); 
	}

	/**
	 * Create reals table.
	 */
	private static function createTableReals() {
		$table = Real::tableName();
		mysql_query("
				CREATE TABLE IF NOT EXISTS $table (
				id          INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id     INTEGER,
				is_active   TINYINT(1),
				city        VARCHAR(255),
				district    VARCHAR(255),
				street      VARCHAR(255),
				area        DECIMAL(12,3),
				price       DECIMAL(12,3),
				rooms       INTEGER,
				floor       INTEGER,
				has_parking INTEGER,
				floors      INTEGER,
				is_sold     INTEGER)")
		or self::dieTNC($table);
	}

	/**
	 * Create ContactInfos table.
	 */
	private static function createTableContactInfos() {
		$table = ContactInfo::tableName();
		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id     INTEGER,
				name        VARCHAR(255),
				surname     VARCHAR(255),
				`e-mail`    VARCHAR(255),
				mobile      VARCHAR(255),
				land_phone  VARCHAR(255),
				attached_to VARCHAR(255),
				attached_id INTEGER)")
		or self::dieTNC($table);
	}

	/**
	 * Create search agents table.
	 *
	 * @return void
	 */
	private static function createTableSearchAgents() {
		$table = SearchAgent::tableName();

		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id          INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name        VARCHAR(255),
				searchable  VARCHAR(255),
				is_active   TINYINT(1),
				last_run_on INTEGER,
				is_running  TINYINT(1),
				is_run      TINYINT(1)
			)") 
			or self::dieTNC($table);
	}

	/**
	 * Create table for search values.
	 *
	 * @return void
	 */
	private static function createTableSearchValues() {
		$table = SearchValue::tableName();

		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id              INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				search_agent_id INTEGER,
				search_field    VARCHAR(255),
				search_value    VARCHAR(255))"
			) or self::dieTNC($table);
	}
}

?>

<h1><?= t("Installation"); ?></h1>
	<p>Connection with database <b><?= Config::$DB_NAME ?></b>
<? 
	if (DB::connect()) echo 'WORKS';
	else die("DOESN'T WORK");
?>
	</p>

	<? Install::createTables(); ?>

<? Install::dsd(); ?>

<? /** Install user */ ?>
	<p>
	<? if (InstallUser::userOk('demo')) { ?>
		Demo user account <b>name</b>- demo, <b>password</b>- demo</b>
	<? } else { ?>
		<b>No demo user exists!</b>
	<? } ?>
	</p>

<?
//Clear session data if there where any sessions.
if(''==session_id()) {
	session_start();
	session_destroy();
}
?>
<?= Html::ab(t("Start using")) ?>

