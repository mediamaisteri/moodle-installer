<?php
namespace Mediamaisteri;

use Composer\IO\IOInterface;
use Composer\Composer;
use Composer\Installers\BaseInstaller;

class MoodleInstaller extends BaseInstaller
{
  protected $locations = array(
        'mod'                => 'mod/{$name}/',
        'admin_report'       => 'admin/report/{$name}/',
        'atto'               => 'lib/editor/atto/plugins/{$name}/',
        'tool'               => 'admin/tool/{$name}/',
        'assignment'         => 'mod/assignment/type/{$name}/',
        'assignsubmission'   => 'mod/assign/submission/{$name}/',
        'assignfeedback'     => 'mod/assign/feedback/{$name}/',
        'auth'               => 'auth/{$name}/',
        'availability'       => 'availability/condition/{$name}/',
        'block'              => 'blocks/{$name}/',
        'booktool'           => 'mod/book/tool/{$name}/',
        'cachestore'         => 'cache/stores/{$name}/',
        'cachelock'          => 'cache/locks/{$name}/',
        'calendartype'       => 'calendar/type/{$name}/',
        'fileconverter'      => 'files/converter/{$name}/',
        'format'             => 'course/format/{$name}/',
        'coursereport'       => 'course/report/{$name}/',
        'customcertelement'  => 'mod/customcert/element/{$name}/',
        'datafield'          => 'mod/data/field/{$name}/',
        'datapreset'         => 'mod/data/preset/{$name}/',
        'editor'             => 'lib/editor/{$name}/',
        'enrol'              => 'enrol/{$name}/',
        'filter'             => 'filter/{$name}/',
        'gradeexport'        => 'grade/export/{$name}/',
        'gradeimport'        => 'grade/import/{$name}/',
        'gradereport'        => 'grade/report/{$name}/',
        'gradingform'        => 'grade/grading/form/{$name}/',
        'local'              => 'local/{$name}/',
        'logstore'           => 'admin/tool/log/store/{$name}/',
        'ltisource'          => 'mod/lti/source/{$name}/',
        'ltiservice'         => 'mod/lti/service/{$name}/',
        'message'            => 'message/output/{$name}/',
        'mnetservice'        => 'mnet/service/{$name}/',
        'plagiarism'         => 'plagiarism/{$name}/',
        'portfolio'          => 'portfolio/{$name}/',
        'qbehaviour'         => 'question/behaviour/{$name}/',
        'qformat'            => 'question/format/{$name}/',
        'qtype'              => 'question/type/{$name}/',
        'quizaccess'         => 'mod/quiz/accessrule/{$name}/',
        'quiz'               => 'mod/quiz/report/{$name}/',
        'report'             => 'report/{$name}/',
        'repository'         => 'repository/{$name}/',
        'scormreport'        => 'mod/scorm/report/{$name}/',
        'search'             => 'search/engine/{$name}/',
        'theme'              => 'theme/{$name}/',
        'tinymce'            => 'lib/editor/tinymce/plugins/{$name}/',
        'profilefield'       => 'user/profile/field/{$name}/',
        'webservice'         => 'webservice/{$name}/',
        'workshopallocation' => 'mod/workshop/allocation/{$name}/',
        'workshopeval'       => 'mod/workshop/eval/{$name}/',
        'workshopform'       => 'mod/workshop/form/{$name}/'    
    );
    protected $composer;
    protected $package;
    protected $io;

    /**
     * Return the install path based on package type.
     *
     * @param  PackageInterface $package
     * @param  string           $frameworkType
     * @return string
     */
    public function getInstallPath(PackageInterface $package, $frameworkType = '')
    {
        $type = $this->package->getType();

        $prettyName = $this->package->getPrettyName();
        if (strpos($prettyName, '/') !== false) {
            list($vendor, $name) = explode('/', $prettyName);
        } else {
            $vendor = '';
            $name = $prettyName;
        }

        $availableVars = $this->inflectPackageVars(compact('name', 'vendor', 'type'));

        $extra = $package->getExtra();
        if (!empty($extra['installer-name'])) {
            $availableVars['name'] = $extra['installer-name'];
        }

        if ($this->composer->getPackage()) {
            $extra = $this->composer->getPackage()->getExtra();
            if (!empty($extra['installer-paths'])) {
                $customPath = $this->mapCustomInstallPaths($extra['installer-paths'], $prettyName, $type, $vendor);
                if ($customPath !== false) {
                    return $this->templatePath($customPath, $availableVars);
                }
            }
        }

        $packageType = substr($type, strlen($frameworkType) + 1);
        $locations = $this->getLocations();
        if (!isset($locations[$packageType])) {
            throw new \InvalidArgumentException(sprintf('Package type "%s" is not supported', $type));
        }

        return $this->templatePath('web/' . $locations[$packageType], $availableVars);
    }

    /**
     * For an installer to override to modify the vars per installer.
     *
     * @param  array<string, string> $vars This will normally receive array{name: string, vendor: string, type: string}
     * @return array<string, string>
     */
    public function inflectPackageVars($vars)
    {
        return $vars;
    }

    /**
     * Gets the installer's locations
     *
     * @return array<string, string> map of package types => install path
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Replace vars in a path
     *
     * @param  string                $path
     * @param  array<string, string> $vars
     * @return string
     */
    protected function templatePath($path, array $vars = array())
    {
        if (strpos($path, '{') !== false) {
            extract($vars);
            preg_match_all('@\{\$([A-Za-z0-9_]*)\}@i', $path, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $var) {
                    $path = str_replace('{$' . $var . '}', $$var, $path);
                }
            }
        }

        return $path;
    }

    /**
     * Search through a passed paths array for a custom install path.
     *
     * @param  array  $paths
     * @param  string $name
     * @param  string $type
     * @param  string $vendor = NULL
     * @return string|false
     */
    protected function mapCustomInstallPaths(array $paths, $name, $type, $vendor = NULL)
    {
        foreach ($paths as $path => $names) {
            $names = (array) $names;
            if (in_array($name, $names) || in_array('type:' . $type, $names) || in_array('vendor:' . $vendor, $names)) {
                return $path;
            }
        }

        return false;
    }

}
