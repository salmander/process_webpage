<?php
// This is global bootstrap for autoloading

Codeception\Util\Autoload::register('', '', dirname( dirname( __FILE__ ) ) . '/app/classes/' );
