<?php
/**
 * Plugin Name: Pricing Table by Supsystic
 * Plugin URI: http://supsystic.com/
 * Description: Pricing Table generator by Supsystic allow you to create responsive pricing tables or comparison table without any programming skills
 * Version: 1.5.5
 * Author: supsystic.com
 * Author URI: http://supsystic.com/
 **/
	/**
	 * Base config constants and functions
	 */
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'config.php');
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'functions.php');
	/**
	 * Connect all required core classes
	 */
    importClassPts('dbPts');
    importClassPts('installerPts');
    importClassPts('baseObjectPts');
    importClassPts('modulePts');
    importClassPts('modelPts');
    importClassPts('viewPts');
    importClassPts('controllerPts');
    importClassPts('helperPts');
    importClassPts('dispatcherPts');
    importClassPts('fieldPts');
    importClassPts('tablePts');
    importClassPts('framePts');
    importClassPts('reqPts');
    importClassPts('uriPts');
    importClassPts('htmlPts');
    importClassPts('responsePts');
    importClassPts('fieldAdapterPts');
    importClassPts('validatorPts');
    importClassPts('errorsPts');
    importClassPts('utilsPts');
    importClassPts('modInstallerPts');
	importClassPts('installerDbUpdaterPts');
	importClassPts('datePts');
	/**
	 * Check plugin version - maybe we need to update database, and check global errors in request
	 */
    installerPts::update();
    errorsPts::init();
    /**
	 * Start application
	 */
    framePts::_()->parseRoute();
    framePts::_()->init();
    framePts::_()->exec();
	
	//var_dump(framePts::_()->getActivationErrors()); exit();
