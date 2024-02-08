<?php
/**
 * @license MIT
 *
 * Modified by J&Co Digital on 08-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

class JcoreBroiler_TestRoutes extends WP_UnitTestCase {

	function testThemeRoute(){
		$template = JcoreBroiler_Routes::load(__DIR__.'/single.php');
		$this->assertTrue($template);
	}

	function testThemeRouteDoesntExist(){
		$template = JcoreBroiler_Routes::load('singlefoo.php');
		$this->assertFalse($template);
	}

	function testFullPathRoute(){
		$hello = WP_CONTENT_DIR.'/plugins/hello.php';
		$template = JcoreBroiler_Routes::load($hello);
		$this->assertTrue($template);
	}

	function testFullPathRouteDoesntExist(){
		$hello = WP_CONTENT_DIR.'/plugins/hello-foo.php';
		$template = JcoreBroiler_Routes::load($hello);
		$this->assertFalse($template);
	}

	function testRouterClass(){
		$this->assertTrue(class_exists('JcoreBroiler_AltoRouter'));
	}

	function testAppliedRoute(){
		$_SERVER['REQUEST_METHOD'] = 'GET';
		global $matches;
		$matches = array();
		$phpunit = $this;
		JcoreBroiler_Routes::map('foo', function() use ($phpunit) {
			global $matches;
			$matches = array();
			$phpunit->assertTrue(true);
			$matches[] = true;
		});
		$this->go_to(home_url('foo'));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testRouteWithVariable() {
		$post_name = 'ziggy';
		$post = $this->factory->post->create(array('post_title' => 'Ziggy', 'post_name' => $post_name));
		global $matches;
		$matches = array();
		$phpunit = $this;
		JcoreBroiler_Routes::map('mything/:slug', function($params) use ($phpunit) {
			global $matches;
			$matches = array();
			if ('ziggy' == $params['slug']) {
				$matches[] = true;
			}
		});
		$this->go_to(home_url('/mything/'.$post_name));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testRouteWithAltoVariable() {
		$post_name = 'ziggy';
		$post = $this->factory->post->create(array('post_title' => 'Ziggy', 'post_name' => $post_name));
		global $matches;
		$matches = array();
		$phpunit = $this;
		JcoreBroiler_Routes::map('mything/[*:slug]', function($params) use ($phpunit) {
			global $matches;
			$matches = array();
			if ('ziggy' == $params['slug']) {
				$matches[] = true;
			}
		});
		$this->go_to(home_url('/mything/'.$post_name));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testRouteWithMultiArguments() {
		$phpunit = $this;
		JcoreBroiler_Routes::map('artist/[:artist]/song/[:song]', function($params) use ($phpunit) {
			global $matches;
			$matches = array();
			if ($params['artist'] == 'smashing-pumpkins') {
				$matches[] = true;
			}
			if ($params['song'] == 'mayonaise') {
				$matches[] = true;
			}
		});
		$this->go_to(home_url('/artist/smashing-pumpkins/song/mayonaise'));
		$this->matchRoutes();
		global $matches;
		$this->assertEquals(2, count($matches));
	}

	function testRouteWithMultiArgumentsOldStyle() {
		$phpunit = $this;
		global $matches;
		JcoreBroiler_Routes::map('studio/:studio/movie/:movie', function($params) use ($phpunit) {
			global $matches;
			$matches = array();
			if ($params['studio'] == 'universal') {
				$matches[] = true;
			}
			if ($params['movie'] == 'brazil') {
				$matches[] = true;
			}
		});
		$this->go_to(home_url('/studio/universal/movie/brazil/'));
		$this->matchRoutes();
		$this->assertEquals(2, count($matches));
	}

	function testRouteAgainstPostName(){
		$post_name = 'jared';
		$post = $this->factory->post->create(array('post_title' => 'Jared', 'post_name' => $post_name));
		global $matches;
		$matches = array();
		$phpunit = $this;
		JcoreBroiler_Routes::map('randomthing/'.$post_name, function() use ($phpunit) {
			global $matches;
			$matches = array();
			$phpunit->assertTrue(true);
			$matches[] = true;
		});
		$this->go_to(home_url('/randomthing/'.$post_name));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testVerySimpleRoute(){
		$_SERVER['REQUEST_METHOD'] = 'GET';
		global $matches;
		$matches = array();
		$phpunit = $this;
		JcoreBroiler_Routes::map('crackers', function() use ($phpunit) {
			global $matches;
			$matches = array();
			$matches[] = true;
		});
		$this->go_to(home_url('crackers'));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testVerySimpleRouteTrailingSlash(){
		$_SERVER['REQUEST_METHOD'] = 'GET';
		global $matches;
		$matches = array();
		$phpunit = $this;
		JcoreBroiler_Routes::map('bip/', function() use ($phpunit) {
			global $matches;
			$matches = array();
			$matches[] = true;
		});
		$this->go_to(home_url('bip'));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testVerySimpleRouteTrailingSlashInRequest(){
		$_SERVER['REQUEST_METHOD'] = 'GET';
		global $matches;
		$matches = array();
		$phpunit = $this;
		JcoreBroiler_Routes::map('bopp', function() use ($phpunit) {
			global $matches;
			$matches = array();
			$matches[] = true;
		});
		$this->go_to(home_url('bopp/'));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}


	function testVerySimpleRouteTrailingSlashInRequestAndMapping(){
		$_SERVER['REQUEST_METHOD'] = 'GET';
		global $matches;
		$matches = array();
		$phpunit = $this;
		JcoreBroiler_Routes::map('zappers', function() use ($phpunit) {
			global $matches;
			$matches = array();
			$matches[] = true;
		});
		$this->go_to(home_url('zappers/'));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testVerySimpleRoutePreceedingSlash(){
		$_SERVER['REQUEST_METHOD'] = 'GET';
		global $matches;
		$matches = array();
		$phpunit = $this;
		JcoreBroiler_Routes::map('/gobbles', function() use ($phpunit) {
			global $matches;
			$matches = array();
			$matches[] = true;
		});
		$this->go_to(home_url('gobbles'));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testFailedRoute(){
		$_SERVER['REQUEST_METHOD'] = 'GET';
		global $matches;
		$matches = array();
		$phpunit = $this;
		JcoreBroiler_Routes::map('foo', function() use ($phpunit){
			$matches = array();
			$phpunit->assertTrue(false);
			$matches[] = true;
		});
		$this->go_to(home_url('bar'));
		$this->matchRoutes();
		$this->assertEquals(0, count($matches));
	}

	function testRouteWithClassCallback() {
		JcoreBroiler_Routes::map('classroute', array('JcoreBroiler_TestRoutes', '_testCallback'));
		$this->go_to(home_url('classroute'));
		$this->matchRoutes();
		global $matches;
		$this->assertEquals(1, count($matches));
	}

	function matchRoutes() {
		global $upstatement_routes;
		$upstatement_routes->match_current_request();
	}

	static function _testCallback() {
		global $matches;
		$matches[] = true;
	}
}
