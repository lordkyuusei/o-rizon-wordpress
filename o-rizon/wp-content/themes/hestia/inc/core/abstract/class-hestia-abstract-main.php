<?php
/**
 * Main abstract function with the injected loader.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Abstract_Main
 */
abstract class Hestia_Abstract_Main {
	/**
	 * Has an instance of the Hestia_Loader class used for adding actions and filters.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     Hestia_Loader $loader A instance of Hestia_Loader.
	 */
	protected $loader;

	/**
	 * Initialize the control. Add all the hooks necessary.
	 */
	abstract public function init();

	/**
	 * Registers the loader.
	 * And setup activate and deactivate hooks.
	 *
	 * @codeCoverageIgnore
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param Hestia_Loader $loader The loader class used to register action hooks and filters.
	 */
	public function register_loader( Hestia_Loader $loader ) {
		$this->loader = $loader;
	}
}
