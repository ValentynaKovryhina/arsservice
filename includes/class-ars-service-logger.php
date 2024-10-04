<?php

/**
 * Logger class.
 *
 * Defines the plugin name, version.
 *
 * @package    Ars_Service
 * @subpackage Ars_Service/includes
 * @author     Imaris <info@imaris.ua>
 */

class Ars_Service_Logger
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	/**
	 * Incremental log, where each entry is an array with the following elements:
	 *
	 *  - timestamp => timestamp in seconds as returned by time()
	 *  - level => severity of the bug; one between debug, info, warning, error
	 *  - name => name of the log entry, optional
	 *  - message => actual log message
	 */
	protected static $log = [];

	/**
	 * Whether to print log entries to screen as they are added.
	 */
	public static $print_log = true;

	/**
	 * Whether to write log entries to file as they are added.
	 */
	public static $write_log = false;

	/**
	 * Directory where the log will be dumped, without final slash; default
	 * is this file's directory
	 */
	public static $log_dir = __DIR__;

	/**
	 * File name for the log saved in the log dir
	 */
	public static $log_file_name = "debug";

	/**
	 * File extension for the logs saved in the log dir
	 */
	public static $log_file_extension = "log";

	/**
	 * Whether to append to the log file (true) or to overwrite it (false)
	 */
	public static $log_file_append = true;

	/**
	 * Set the maximum level of logging to write to logs
	 */
	// public static $log_level = 'error';
	public static $log_level = 'debug';

	/**
	 * Name for the default timer
	 */
	public static $default_timer = 'timer';

	/**
	 * Map logging levels to syslog specifications, there's room for the other levels
	 */
	private static $log_level_integers = [
		'debug' => 7,
		'info' => 6,
		'warning' => 4,
		'error' => 3
	];

	/**
	 * Absolute path of the log file, built at run time
	 */
	private static $log_file_path = '';

	/**
	 * Where should we write/print the output to? Built at run time
	 */
	private static $output_streams = [];

	/**
	 * Whether the init() function has already been called
	 */
	private static $logger_ready = false;

	/**
	 * Associative array used as a buffer to keep track of timed logs
	 */
	private static $time_tracking = [];

	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Add a log entry with a diagnostic message for the developer.
	 */
	public static function debug($message, $name = '')
	{
		return self::add($message, $name, 'debug');
	}

	/**
	 * Add a log entry with an informational message for the user.
	 */
	public static function info($message, $name = '')
	{
		return self::add($message, $name, 'info');
	}

	/**
	 * Add a log entry with a warning message.
	 */
	public static function warning($message, $name = '')
	{
		return self::add($message, $name, 'warning');
	}

	/**
	 * Add a log entry with an error - usually followed by
	 * script termination.
	 */
	public static function error($message, $name = '')
	{
		return self::add($message, $name, 'error');
	}

	/**
	 * Start counting time, using $name as identifier.
	 *
	 * Returns the start time or false if a time tracker with the same name
	 * exists
	 */
	public static function time(string $name = null)
	{

		if ($name === null) {
			$name = self::$default_timer;
		}

		if (!isset(self::$time_tracking[$name])) {
			self::$time_tracking[$name] = microtime(true);
			return self::$time_tracking[$name];
		} else {
			return false;
		}
	}


	/**
	 * Stop counting time, and create a log entry reporting the elapsed amount of
	 * time.
	 *
	 * Returns the total time elapsed for the given time-tracker, or false if the
	 * time tracker is not found.
	 */
	public static function timeEnd(string $name = null, int $decimals = 6, $level = 'debug')
	{

		$is_default_timer = $name === null;

		if ($is_default_timer) {
			$name = self::$default_timer;
		}

		if (isset(self::$time_tracking[$name])) {
			$start = self::$time_tracking[$name];
			$end = microtime(true);
			$elapsed_time = number_format(($end - $start), $decimals);
			unset(self::$time_tracking[$name]);
			if (!$is_default_timer) {
				self::add("$elapsed_time seconds", "Elapsed time for '$name'", $level);
			} else {
				self::add("$elapsed_time seconds", "Elapsed time", $level);
			}
			return $elapsed_time;
		} else {
			return false;
		}
	}


	/**
	 * Add an entry to the log.
	 *
	 * This function does not update the pretty log.
	 */
	private static function add($message, $name = '', $level = 'debug')
	{
		/* Check if the logging level severity warrants writing this log */
		if (self::$log_level_integers[$level] > self::$log_level_integers[self::$log_level]) {
			return;
		}

		/* Create the log entry */
		$log_entry = [
			'timestamp' => time(),
			'name' => $name,
			'message' => $message,
			'level' => $level,
		];

		/* Add the log entry to the incremental log */
		self::$log[] = $log_entry;

		/* Initialize the logger if it hasn't been done already */
		if (!self::$logger_ready) {
			self::init();
		}

		/* Write the log to output, if requested */
		if (self::$logger_ready && count(self::$output_streams) > 0) {
			$output_line = self::format_log_entry($log_entry) . PHP_EOL;
			foreach (self::$output_streams as $key => $stream) {
				fputs($stream, $output_line);
			}
		}

		return $log_entry;
	}

	/**
	 * Take one log entry and return a one-line human readable string
	 */
	public static function format_log_entry(array $log_entry): string
	{

		$log_line = "";

		if (!empty($log_entry)) {

			/* Make sure the log entry is stringified */
			$log_entry = array_map(function ($v) {
				return print_r($v, true);
			}, $log_entry);

			/* Build a line of the pretty log */
			$log_line .= date('c', $log_entry['timestamp']) . " ";
			$log_line .= "[" . strtoupper($log_entry['level']) . "] ";
			if (!empty($log_entry['name'])) {
				$log_line .= $log_entry['name'] . " => ";
			}
			$log_line .= $log_entry['message'];
		}

		return $log_line;
	}


	/**
	 * Determine whether an where the log needs to be written; executed only
	 * once.
	 *
	 * @return {array} - An associative array with the output streams. The
	 * keys are 'output' for STDOUT and the filename for file streams.
	 */
	public static function init()
	{
		self::$logger_ready = false;
		if (!self::$logger_ready) {

			$path_to_directory = str_replace('/', DIRECTORY_SEPARATOR, WP_CONTENT_DIR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'ars-log';
			if (!file_exists($path_to_directory)) {
				mkdir($path_to_directory, 0777, true);
			}
			self::$log_dir = $path_to_directory;
			self::$print_log = false;
			self::$write_log = true;
			self::$log_level = 'debug';

			/* Удаляем старые файлы старше 3 месяцев */
			self::delete_old_logs(self::$log_dir);

			/* Print to screen */
			if (true === self::$print_log) {
				self::$output_streams['stdout'] = STDOUT;
			}

			/* Build log file path */
			if (file_exists(self::$log_dir)) {
				self::$log_file_path = implode(DIRECTORY_SEPARATOR, [self::$log_dir, self::$log_file_name . date('-Y-m-d')]);
				if (!empty(self::$log_file_extension)) {
					self::$log_file_path .= "." . self::$log_file_extension;
				}
			}

			/* Print to log file */
			if (true === self::$write_log) {
				if (file_exists(self::$log_dir)) {
					$mode = self::$log_file_append ? "a" : "w";
					self::$output_streams[self::$log_file_path] = fopen(self::$log_file_path, $mode);
				}
			}
		}

		/* Now that we have assigned the output stream, this function does not need
		to be called anymore */
		self::$logger_ready = true;
	}

	public static function delete_old_logs($directory)
	{
		$files = scandir($directory);
		$current_time = time();
		$three_months_ago = strtotime('-3 months');

		foreach ($files as $file) {
			$file_path = $directory . DIRECTORY_SEPARATOR . $file;

			// Проверяем, что это файл, а не директория
			if (is_file($file_path)) {
				// Получаем время изменения файла
				$file_modification_time = filemtime($file_path);

				// Если файл старше 3 месяцев, удаляем его
				if ($file_modification_time < $three_months_ago) {
					unlink($file_path);
				}
			}
		}
	}


	/**
	 * Dump the whole log to the given file.
	 *
	 * Useful if you don't know before-hand the name of the log file. Otherwise,
	 * you should use the real-time logging option, that is, the $write_log or
	 * $print_log options.
	 *
	 * The method format_log_entry() is used to format the log.
	 *
	 * @param {string} $file_path - Absolute path of the output file. If empty,
	 * will use the class property $log_file_path.
	 */
	public static function dump_to_file($file_path = '')
	{

		if (!$file_path) {
			$file_path = self::$log_file_path;
		}

		if (file_exists(dirname($file_path))) {

			$mode = self::$log_file_append ? "a" : "w";
			$output_file = fopen($file_path, $mode);

			foreach (self::$log as $log_entry) {
				$log_line = self::format_log_entry($log_entry);
				fwrite($output_file, $log_line . PHP_EOL);
			}

			fclose($output_file);
		}
	}


	/**
	 * Dump the whole log to string, and return it.
	 *
	 * The method format_log_entry() is used to format the log.
	 */
	public static function dump_to_string()
	{

		$output = '';

		foreach (self::$log as $log_entry) {
			$log_line = self::format_log_entry($log_entry);
			$output .= $log_line . PHP_EOL;
		}

		return $output;
	}

	/**
	 * Empty the log
	 */
	public static function clear_log()
	{
		self::$log = [];
	}
}
