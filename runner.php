<?
include 'includes.php';
include 'runner.class.php';
DB::connect();

Runner::run_tasks();

