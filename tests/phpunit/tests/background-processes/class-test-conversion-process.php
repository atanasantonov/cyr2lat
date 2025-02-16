<?php
/**
 * Test_Conversion_Process class file
 *
 * @package cyr-to-lat
 */

// phpcs:disable Generic.Commenting.DocComment.MissingShort
/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
// phpcs:enable Generic.Commenting.DocComment.MissingShort

namespace Cyr_To_Lat;

use Mockery;
use stdClass;
use tad\FunctionMocker\FunctionMocker;
use WP_Mock;

/**
 * Class Test_Conversion_Process
 *
 * @group process
 */
class Test_Conversion_Process extends Cyr_To_Lat_TestCase {

	/**
	 * Test task()
	 */
	public function test_task() {
		$subject = Mockery::mock( Conversion_Process::class )->makePartial()->shouldAllowMockingProtectedMethods();

		self::assertFalse( $subject->task( new stdClass() ) );
	}

	/**
	 * Test complete()
	 */
	public function test_complete() {
		$subject = Mockery::mock( Conversion_Process::class )->makePartial()->shouldAllowMockingProtectedMethods();

		WP_Mock::userFunction(
			'wp_next_scheduled',
			[
				'return' => null,
				'times'  => 1,
			]
		);

		WP_Mock::userFunction(
			'set_site_transient',
			[
				'times' => 1,
			]
		);

		$subject->complete();
	}

	/**
	 * Test is_process_completed()
	 *
	 * @param mixed   $transient Transient.
	 * @param boolean $expected  Expected result.
	 *
	 * @dataProvider dp_test_is_process_completed
	 */
	public function test_is_process_completed( $transient, $expected ) {
		$main    = Mockery::mock( Main::class );
		$subject = new Conversion_Process( $main );

		WP_Mock::userFunction(
			'get_site_transient',
			[
				'args'   => [ $this->cyr_to_lat_prefix . '_background_process_process_completed' ],
				'return' => $transient,
				'times'  => 1,
			]
		);

		if ( $transient ) {
			WP_Mock::userFunction(
				'delete_site_transient',
				[
					'args'  => [ $this->cyr_to_lat_prefix . '_background_process_process_completed' ],
					'times' => 1,
				]
			);
		}

		self::assertSame( $expected, $subject->is_process_completed() );
	}

	/**
	 * Data provider for test_is_process_completed()
	 */
	public function dp_test_is_process_completed() {
		return [
			[ true, true ],
			[ false, false ],
		];
	}

	/**
	 * Test is_process_running()
	 *
	 * @param mixed   $transient Transient.
	 * @param boolean $expected  Expected result.
	 *
	 * @dataProvider dp_test_is_process_running
	 */
	public function test_is_process_running( $transient, $expected ) {
		$main    = Mockery::mock( Main::class );
		$subject = new Conversion_Process( $main );

		WP_Mock::userFunction(
			'get_site_transient',
			[
				'args'   => [ $this->cyr_to_lat_prefix . '_background_process_process_lock' ],
				'return' => $transient,
				'times'  => 1,
			]
		);

		self::assertSame( $expected, $subject->is_process_running() );
	}

	/**
	 * Data provider for test_is_process_running()
	 */
	public function dp_test_is_process_running() {
		return [
			[ true, true ],
			[ false, false ],
		];
	}

	/**
	 * Test log()
	 *
	 * @param boolean $debug Is WP_DEBUG_LOG on.
	 *
	 * @dataProvider        dp_test_log
	 */
	public function test_log( $debug ) {
		$subject = Mockery::mock( Conversion_Process::class )->makePartial()->shouldAllowMockingProtectedMethods();

		$message = 'Test message';

		FunctionMocker::replace(
			'defined',
			function ( $name ) use ( $debug ) {
				if ( 'WP_DEBUG_LOG' === $name ) {
					return $debug;
				}

				return null;
			}
		);

		FunctionMocker::replace(
			'constant',
			function ( $name ) use ( $debug ) {
				if ( 'WP_DEBUG_LOG' === $name ) {
					return $debug;
				}

				return null;
			}
		);

		$log = [];
		FunctionMocker::replace(
			'error_log',
			function ( $message ) use ( &$log ) {
				$log[] = $message;
			}
		);

		$subject->log( $message );
		if ( $debug ) {
			self::assertSame( [ 'Cyr To Lat: ' . $message ], $log );
		} else {
			self::assertSame( [], $log );
		}
	}

	/**
	 * Data provider for test_log()
	 *
	 * @return array
	 */
	public function dp_test_log() {
		return [
			[ false ],
			[ true ],
		];
	}
}
