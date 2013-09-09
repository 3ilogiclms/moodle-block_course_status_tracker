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
 * @author: Azmat Ullah, Talha Noor
 */
require_once("{$CFG->libdir}/formslib.php");
require_once("lib.php");

/**
 * This class contains function display_report which return user course name, course grade & completion date.
 */
class course_status_form extends moodleform {
    public function definition() {
        return false;
    }
    public function display_report() {
        global $DB, $OUTPUT, $CFG, $USER;
        $userid = $USER->id;
        // Page parameters.
        $page    = optional_param('page', 0, PARAM_INT);
        $perpage = optional_param('perpage', 30, PARAM_INT);    // How many record per page.
        $sort    = optional_param('sort', 'firstname', PARAM_ALPHA);
        $dir     = optional_param('dir', 'DESC', PARAM_ALPHA);
        $sql = "SELECT course, gradefinal, DATE_FORMAT(DATE( FROM_UNIXTIME(timecompleted)),'%d-%b-%y')as dates FROM {course_completion_crit_compl} where userid = ".$userid;
        $changescount = $DB->count_records_sql($sql, array($userid));
        $columns = array('s_no' => get_string('s_no', 'block_course_status'),
                         'course_name' => get_string('course_name', 'block_course_status'),
                         'course_comp_date' => get_string('course_comp_date', 'block_course_status'),
                         'grade' => get_string('grade', 'block_course_status'),
                        );
        $hcolumns = array();
        if (!isset($columns[$sort])) {
            $sort = 's_no';
        }
        foreach ($columns as $column => $strcolumn) {
            if ($sort != $column) {
                $columnicon = '';
                if ($column == 's_no') {
                    $columndir = 'DESC';
                } else {
                    $columndir = 'ASC';
                }
            } else {
                $columndir = $dir == 'ASC' ? 'DESC':'ASC';
                if ($column == 's_no') {
                    $columnicon = $dir == 'ASC' ? 'up':'down';
                } else {
                    $columnicon = $dir == 'ASC' ? 'down':'up';
                }
                $columnicon = " <img src=\"" . $OUTPUT->pix_url('t/' . $columnicon) . "\" alt=\"\" />";
            }
            $hcolumns[$column] = "<a href=\"view.php?viewpage=1&sort=$column&amp;dir=$columndir&amp;page=$page&amp;perpage=$perpage\">".$strcolumn."</a>$columnicon";
        }
        $baseurl = new moodle_url('view.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
        echo $OUTPUT->paging_bar($changescount, $page, $perpage, $baseurl);
        $table = new html_table();
        $table->head  = array('Serial No.', 'Course Name', 'Course Completion Date', 'Grade');
        $table->size  = array('10%', '30%', '40%', '20%');
        $table->align  = array('center', 'left', 'center', 'center');
        $table->width = '80%';
        $table->data  = array();
        $orderby = "$sort $dir";
        $rs = $DB->get_recordset_sql($sql, array(),  $page*$perpage, $perpage);
        $i=0;
        foreach ($rs as $log) {
            $row = array();
            $row[] = ++$i;
            $row[] = course_name($log->course);
            $row[] = $log->dates;
            $row[] = round($log->gradefinal).'%';
            $table->data[] = $row;
        }
        return $table;
    }
}
