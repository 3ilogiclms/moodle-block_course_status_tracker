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

/**
 * Block to display enrolled, completed, inprogress and undefined courses according to course completion criteria named 'grade' based on login user.
 *
 * @package    block_course_status_tracker
 * @copyright  3i Logic<lms@3ilogic.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
 
require_once('../../config.php');
require_once('course_form.php');
require_once('lib.php');
require_login();
global $DB, $OUTPUT, $PAGE, $CFG, $USER;
$context = context_system::instance();
$viewpage = required_param('viewpage', PARAM_INT);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string("pluginname", 'block_course_status_tracker'));
$PAGE->set_heading('Course Status');
$pageurl = '/blocks/course_status_tracker/view.php?viewpage=' . $viewpage;
$PAGE->set_url($pageurl);
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string("pluginname", 'block_course_status_tracker'));
echo $OUTPUT->header();
?>

<!-- DataTables code starts-->
<link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot ?>/blocks/course_status_tracker/public/datatable/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot ?>/blocks/course_status_tracker/public/datatable/dataTables.tableTools.css">
<script type="text/javascript" language="javascript" src="<?php echo $CFG->wwwroot ?>/blocks/course_status_tracker/public/datatable/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $CFG->wwwroot ?>/blocks/course_status_tracker/public/datatable/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $CFG->wwwroot ?>/blocks/course_status_tracker/public/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" language="javascript" class="init">
    $(document).ready(function () {
        // fn for automatically adjusting table coulmns
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
        });

        $('.display').DataTable({
            dom: 'T<"clear">lfrtip',
            tableTools: {
                "aButtons": [
                    "copy",
                    "print",
                    {
                        "sExtends": "collection",
                        "sButtonText": "Save",
                        "aButtons": ["xls", "pdf"]
                    }
                ],
                "sSwfPath": "<?php echo $CFG->wwwroot ?>/blocks/course_status_tracker/public/datatable/copy_csv_xls_pdf.swf"
            }
        });
    });
</script>
<!-- DataTables code ends-->

<?php
if ($viewpage == 1) {
    $form = new course_status_form();
    $table = $form->display_report();
    if ($table) {
        echo "<div id='prints'>";
        // $title = '<center><table width="80%" style="background-color:#F3F3F3;"><tr><td><center><h2>' . get_string('report_coursecompletion', 'block_course_status_tracker') . '</h2></center></td></tr></tr><table></center>';
        $title = '<h2>' . get_string('report_coursecompletion', 'block_course_status_tracker') . '</h2>';
        $title.=user_details($USER->id);
        $a = html_writer::table($table);
        echo $title;
        echo "<br/>".$a;
        echo "</div>";
    }
} else if ($viewpage == 2) {
    echo "<div id='prints'>";
    // $title = '<center><table width="80%" style="background-color:#F3F3F3;"><tr><td><center><h2>' . get_string('report_courseenrollment', 'block_course_status_tracker') . '</h2></center></td></tr></tr><table></center>';
    $title = '<h2>' . get_string('report_courseenrollment', 'block_course_status_tracker') . '</h2>';
    $title.=user_details($USER->id);
    echo $title;
    $a = html_writer::table(user_enrolled_courses_report($USER->id));
    echo "<br/>".$a;
    echo "</div>";
} else if ($viewpage == 3) {
    echo "<div id='prints'>";
    $title = '<h2>' . get_string('report_courseundefined', 'block_course_status_tracker') . '</h2>';
    $title.=user_details($USER->id);
    echo $title;
	echo "<br/>".html_writer::table(user_undefined_courses_report($USER->id));
    echo "</div>";
} else if ($viewpage == 4) {
    echo "<div id='prints'>";
    $title = '<h2>' . get_string('report_courseinprogress', 'block_course_status_tracker') . '</h2>';
    $title.=user_details($USER->id);
    echo $title;
	echo "<br/>".html_writer::table(user_inprogress_courses_report($USER->id));
    echo "</div>";
} 
else
    header($CFG->wwwroot);
echo $OUTPUT->footer();
