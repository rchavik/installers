<?php
namespace Baton\Test;

use Baton\Installer;
use Composer\Util\Filesystem;
use Composer\Package\MemoryPackage;

class InstallerTest extends TestCase
{

    private $vendorDir;
    private $binDir;
    private $dm;
    private $repository;
    private $io;
    private $fs;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        $this->fs = new Filesystem;

        $this->vendorDir = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'baton-test-vendor';
        $this->ensureDirectoryExistsAndClear($this->vendorDir);

        $this->binDir = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'baton-test-bin';
        $this->ensureDirectoryExistsAndClear($this->binDir);

        $this->dm = $this->getMockBuilder('Composer\Downloader\DownloadManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMock('Composer\Repository\InstalledRepositoryInterface');

        $this->io = $this->getMock('Composer\IO\IOInterface');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        $this->fs->removeDirectory($this->vendorDir);
        $this->fs->removeDirectory($this->binDir);
    }

    /**
     * testSupports
     *
     * @return void
     */
    public function testSupports()
    {
        $types = array(
            'cakephp', 'codeigniter', 'fuelphp',
            'laravel', 'lithium',
        );
        $Installer = new Installer($this->vendorDir, $this->binDir, $this->dm, $this->io);
        foreach ($types as $type) {
            $this->assertTrue($Installer->supports($type));
        }
    }

    /**
     * testGetCakePHPInstallPath
     *
     * @return void
     */
    public function testGetCakePHPInstallPath()
    {
        $Installer = new Installer($this->vendorDir, $this->binDir, $this->dm, $this->io);
        $Package = new MemoryPackage('shama/Ftp', '1.0.0', '1.0.0');

        $Package->setType('cakephp-plugin');
        $result = $Installer->getInstallPath($Package);
        $this->assertEquals('/Plugin/Ftp/', $result);

        $Package->setType('cakephp-whoops');
        $result = $Installer->getInstallPath($Package);
        $this->assertEquals('/Vendor/Ftp/', $result);
    }

    /**
     * testGetLithiumInstallPath
     *
     * @return void
     */
    public function testGetLithiumInstallPath()
    {
        $Installer = new Installer($this->vendorDir, $this->binDir, $this->dm, $this->io);
        $Package = new MemoryPackage('user/li3_test', '1.0.0', '1.0.0');

        $Package->setType('lithium-libraries');
        $result = $Installer->getInstallPath($Package);
        $this->assertEquals('/libraries/li3test/', $result);
    }

}