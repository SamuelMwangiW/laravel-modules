<?php

namespace Nwidart\Modules\Tests\Commands;

use Nwidart\Modules\Contracts\RepositoryInterface;
use Nwidart\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class EventMakeCommandTest extends BaseTestCase
{
    use MatchesSnapshots;
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $finder;

    /**
     * @var string
     */
    private $modulePath;

    public function setUp(): void
    {
        parent::setUp();
        $this->finder = $this->app['files'];
        $this->createModule();
        $this->modulePath = $this->getModuleAppPath();
    }

    public function tearDown(): void
    {
        $this->app[RepositoryInterface::class]->delete('Blog');
        parent::tearDown();
    }

    public function test_it_generates_a_new_event_class()
    {
        $code = $this->artisan('module:make-event', ['name' => 'PostWasCreated', 'module' => 'Blog']);

        $this->assertTrue(is_file($this->modulePath . '/Events/PostWasCreated.php'));
        $this->assertSame(0, $code);
    }

    public function test_it_generated_correct_file_with_content()
    {
        $code = $this->artisan('module:make-event', ['name' => 'PostWasCreated', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath . '/Events/PostWasCreated.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    public function test_it_can_change_the_default_namespace()
    {
        $this->app['config']->set('modules.paths.generator.event.path', 'SuperEvents');

        $code = $this->artisan('module:make-event', ['name' => 'PostWasCreated', 'module' => 'Blog']);

        $file = $this->finder->get($this->getModuleBasePath() . '/SuperEvents/PostWasCreated.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }

    public function test_it_can_change_the_default_namespace_specific()
    {
        $this->app['config']->set('modules.paths.generator.event.namespace', 'SuperEvents');

        $code = $this->artisan('module:make-event', ['name' => 'PostWasCreated', 'module' => 'Blog']);

        $file = $this->finder->get($this->modulePath . '/Events/PostWasCreated.php');

        $this->assertMatchesSnapshot($file);
        $this->assertSame(0, $code);
    }
}
