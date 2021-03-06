<?php
/**
 * @group themes
 */
class Tests_Admin_includesTheme extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->theme_root = DIR_TESTDATA . '/themedir1';

		$this->orig_theme_dir = $GLOBALS['wp_theme_directories'];
		$GLOBALS['wp_theme_directories'] = array( WP_CONTENT_DIR . '/themes', $this->theme_root );

		add_filter('theme_root', array($this, '_theme_root'));
		add_filter( 'stylesheet_root', array($this, '_theme_root') );
		add_filter( 'template_root', array($this, '_theme_root') );

		// clear caches
		wp_clean_themes_cache();
		unset( $GLOBALS['wp_themes'] );
	}

	function tearDown() {
		$GLOBALS['wp_theme_directories'] = $this->orig_theme_dir;
		remove_filter('theme_root', array($this, '_theme_root'));
		remove_filter( 'stylesheet_root', array($this, '_theme_root') );
		remove_filter( 'template_root', array($this, '_theme_root') );

		wp_clean_themes_cache();
		unset( $GLOBALS['wp_themes'] );
		parent::tearDown();
	}

	// replace the normal theme root dir with our premade test dir
	function _theme_root($dir) {
		return $this->theme_root;
	}

	/**
	 * @ticket 10959
	 * @ticket 11216
	 * @expectedDeprecated get_theme
	 * @expectedDeprecated get_themes
	 */
	function test_page_templates() {
		$theme = get_theme( 'Page Template Theme' );
		$this->assertNotEmpty( $theme );

		switch_theme( $theme['Template'], $theme['Stylesheet'] );

		$this->assertEqualSetsWithIndex( array(
			'Top Level'                           => 'template-top-level.php',
			'Sub Dir'                             => 'subdir/template-sub-dir.php',
			'This Template Header Is On One Line' => 'template-header.php',
		), get_page_templates() );

		$theme = wp_get_theme( 'page-templates' );
		$this->assertNotEmpty( $theme );

		switch_theme( $theme['Template'], $theme['Stylesheet'] );

		$this->assertEqualSetsWithIndex( array(
			'Top Level'                           => 'template-top-level.php',
			'Sub Dir'                             => 'subdir/template-sub-dir.php',
			'This Template Header Is On One Line' => 'template-header.php',
		), get_page_templates() );
	}

	/**
	 * @ticket 18375
	 */
	function test_page_templates_different_post_types() {
		$theme = wp_get_theme( 'page-templates' );
		$this->assertNotEmpty( $theme );

		switch_theme( $theme['Template'], $theme['Stylesheet'] );

		$this->assertEqualSetsWithIndex( array(
			'Top Level' => 'template-top-level-post-types.php',
			'Sub Dir'   => 'subdir/template-sub-dir-post-types.php',
		), get_page_templates( null, 'foo' ) );
		$this->assertEqualSetsWithIndex( array(
			'Top Level' => 'template-top-level-post-types.php',
			'Sub Dir'   => 'subdir/template-sub-dir-post-types.php',
		), get_page_templates( null, 'post' ) );
		$this->assertEquals( array(), get_page_templates( null, 'bar' ) );
	}
}
