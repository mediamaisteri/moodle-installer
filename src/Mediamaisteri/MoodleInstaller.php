<?php

namespace Mediamaisteri;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

/**
 * Installs both Moodle and its plugins under web/ directory.
 *
 * Project file structure will become:
 *  - composer.json
 *  - composer.lock
 *  - vendor/
 *  - web/
 *
 * Moodle root will be located in the web/ directory, and it's
 * plugins in corresponding type specific subdirectories such
 * as web/mod/ and web/local/.
 */
class MoodleInstaller extends LibraryInstaller {

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package) {
        $prettyName = $package->getPrettyName();

        if ($prettyName == 'moodle/moodle') {
            return 'web/';
        }

        if (strpos($prettyName, '/') !== false) {
            list($vendor, $name) = explode('/', $prettyName);
        } else {
            $vendor = '';
            $name = $prettyName;
        }

        $availableVars = compact('name', 'vendor');

        $extra = $package->getExtra();
        if (!empty($extra['installer-name'])) {
            $availableVars['name'] = $extra['installer-name'];
        }

        $pluginType = str_replace('moodle-', '', $package->getType());
        var_dump($package->getPrettyName() . ': ' . $package->getType());
        return 'web/' . $this->templatePath($this->locations[$pluginType], $availableVars);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType) {
        $prefix = substr($packageType, 0, 7);

        if ($prefix == 'moodle-') {
            return TRUE;
        }

        return 'project' === $packageType;
    }

    /**
     * Replace vars in a path
     *
     * @param  string $path
     * @param  array  $vars
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
        'format'             => 'course/format/{$name}/',
        'coursereport'       => 'course/report/{$name}/',
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
}
