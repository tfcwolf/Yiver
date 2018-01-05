<?php
interface Command {
	public function run();
	public function parse();
	public function init();
}