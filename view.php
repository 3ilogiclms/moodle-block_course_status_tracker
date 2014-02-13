<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/* Course Status Tracker Block
 * The plugin shows the number and list of enrolled courses and completed courses.
 * It also shows the number of courses which are in progress and whose completion criteria is undefined but the manger.
 * @package blocks
 * @author: Azmat Ullah, Talha Noor, Michael Milette (www.instruxmedia.com)
 */
require_once('../../config.php');
require_once('course_form.php');
require_once('lib.php');

require_login();

global $DB, $OUTPUT, $PAGE, $CFG, $USER;
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string("pluginname", 'block_course_status_tracker'));
$PAGE->set_heading('Course Status');
$pageurl = new moodle_url('/blocks/course_status_tracker/view.php');
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string("pluginname", 'block_course_status_tracker'));
echo $OUTPUT->header();

$viewpage = required_param('viewpage', PARAM_INT);
if($viewpage == 1) {
    $form = new course_status_form();
    $table=$form->display_report();
    if($table) {
        echo "<div id='prints'>";
        $title = '<h2>'.get_string('report_coursecompletion', 'block_course_status_tracker').'</h2>';
        $title.=user_details($USER->id);
        $a= html_writer::table($table);
        echo $title;
        echo $a;
        echo "</div>";
    }
} else if ($viewpage == 2) {
    echo "<div id='prints'>";
    $title = '<h2>'.get_string('report_courseenrollment', 'block_course_status_tracker').'</h2>';
    $title.=user_details($USER->id);
    echo $title;
    $a= html_writer::table(user_enrolled_courses_report($USER->id));
    echo $a;
    echo "</div>";
} else if ($viewpage == 3) {
    echo "<div id='prints'>";
    $title = '<h2>'.get_string('report_courseenrollment', 'block_course_status_tracker').'</h2>';
    $title.=user_details($USER->id);
    echo $title;
    echo  html_writer::table(user_enrolled_courses_report($USER->id));
    echo "</div>";
} else if ($viewpage == 4) {
    echo "<div id='prints'>";
    $title = '<h2>'.get_string('report_courseenrollment', 'block_course_status_tracker').'</h2>';
    $title.=user_details($USER->id);
    echo $title;
    echo  html_writer::table(user_enrolled_courses_report($USER->id));
    echo "</div>";
} else {
    header($CFG->wwwroot);
}

// $reporthtml=$a;
// require_once('../../printemailpdf/displaybutton.php');

echo $OUTPUT->footer();
