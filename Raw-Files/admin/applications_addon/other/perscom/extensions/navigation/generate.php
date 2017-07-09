<?php
/*
+--------------------------------------------------------------------------
|   PERSCOM v1.0
|   =============================================
|   by 3rd Infantry Division (Erickson)
|   Copyright 2014-2015 Third Infantry Division
|   http://www.3rdinf.us
+--------------------------------------------------------------------------
*/

// Check to make sure the user is not accessing the files directly
if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class navigation_perscom
{
	/**
	 * Registry Object Shortcuts
	 *
	 * @var		$registry
	 * @var		$DB
	 * @var		$settings
	 * @var		$request
	 * @var		$lang
	 * @var		$member
	 * @var		$memberData
	 * @var		$cache
	 * @var		$caches
	 */
	protected $registry;

	/**
	 * Constructor
	 *
	 * @return	@e void
	 */
	public function __construct()
	{
		// Get the registry
		$this->registry = ipsRegistry::instance();
	}

	/**
	 * Return the tab title
	 *
	 * @return	@e string
	 */
	public function getTabName()
	{
		return IPSLib::getAppTitle( 'perscom' );
	}

	/**
	 * Returns navigation data
	 *
	 * @return	@e array
	 */
	public function getNavigationData()
	{
		// Create a blocks array variable
		$blocks	= array();

		/* Add to blocks */
		$blocks[] = array(
						'title'	=> IPSLib::getAppTitle( 'perscom' ), /* This is NOT required, but will give your block a 'heading' */
						'links'	=> array(
								/*
									'important' determines if the link should be bolded/highlighted
									'depth' controls how far the link is indented - this is useful for structured links, like forums + subforums
									'title' is the title for the link
									'url' is the link url
								*/
								array( 'important' => true, 'depth' => 0, 'title' => "Base Camp", 'url' => '?app=perscom' ),
								array( 'important' => false, 'depth' => 1, 'title' => "Personnel Files", 'url' => '?app=perscom&module=personnel' ),
								array( 'important' => false, 'depth' => 1, 'title' => "Ranks and Insignia", 'url' => '?app=perscom&module=ranks' ),
								array( 'important' => false, 'depth' => 1, 'title' => "Awards and Commendations", 'url' => '?app=perscom&module=awards' ),
								array( 'important' => false, 'depth' => 1, 'title' => "Operations Center", 'url' => '?app=perscom&module=basecamp&section=information' ),
								)
						);

		// Return the navagation data array
		return $blocks;
	}
}