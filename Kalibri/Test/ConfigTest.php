<?php



namespace Kalibri\Test;

include '../_init.php';

class ConfigTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Config
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = new Config;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{

	}

	/**
	 * Generated from @assert ("path.to.item") == null.
	 *
	 * @covers Kalibri\Config::get
	 */
	public function testGet(): void
	{
		$this->assertEquals(
				null
				, $this->object->get("path.to.item")
		);
	}

	/**
	 * Generated from @assert ("path.to.item", true) == true.
	 *
	 * @covers Kalibri\Config::get
	 */
	public function testGet2(): void
	{
		$this->assertEquals(
				true
				, $this->object->get("path.to.item", true)
		);
	}

	/**
	 * @covers Kalibri\Config::load
	 * @todo   Implement testLoad().
	 */
	public function testLoad(): void
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

}
