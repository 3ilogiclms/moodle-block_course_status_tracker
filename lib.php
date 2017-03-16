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
 * Block to display enrolled, completed, inprogress and undefined courses according to course
 * completi  on criteria named 'grade' based on login user.
 *
 * @package    block_course_status_tracker
 * @copyright  3i Logic<lms@3ilogic.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

/**
 * This function return category id based on course id.
 *
 * @param int   $id course id
 * @return String category id.
 */
function get_category_id($course) {
    global $DB;

    $category_id_sql = "SELECT category FROM {course} where id = " . $course;
    $category_id_rs = $DB->get_record_sql($category_id_sql, array());
    if ($DB->record_exists_sql($category_id_sql, array())) {
        return $category_id_rs->category;
    }
}

/**
 * This function display list of inprogress courses based on login user.
 *
 * @param int   $id user id
 * @return Table table.
 */
function user_inprogress_courses_report($userid) {
    global $CFG, $DB;

    $courses = enrol_get_users_courses($userid, false, 'id, shortname, showgrades');
    if ($courses) {
        $undefined_courses = '';
        $enroll_courses = '';
        foreach ($courses as $course) {
            $exist = $DB->record_exists('course_completion_criteria', array('course' => $course->id));
            $enroll_courses .= $course->id . ",";
            if (!$exist) {
                $undefined_courses .= $course->id . ",";
            }
        }
    }



    $complete_course_sql = "SELECT course FROM {course_completion_crit_compl} where userid = " . $userid;
    $complete_course_rs = $DB->get_records_sql($complete_course_sql, array());
    if ($DB->record_exists_sql($complete_course_sql, array())) {
        $complete_courses = '';
        foreach ($complete_course_rs as $complete_course_log) {
            $complete_courses .= $complete_course_log->course . ",";
        }
    }

    $enrolled_courses = rtrim($enroll_courses, ',');
    $comp_undefined_courses = rtrim($undefined_courses, ',') . "," . rtrim($complete_courses, ',');


    $explode_enrolled_courses = explode(',', $enrolled_courses);
    $enrolled_courses_array = array();
    foreach ($explode_enrolled_courses as $explode_enroll) {
        $enrolled_courses_array[] = $explode_enroll;
    }


    $explode_comp_undefined_courses = explode(',', $comp_undefined_courses);
    $comp_undefined_courses_array = array();
    foreach ($explode_comp_undefined_courses as $explode_comp_undefined_courses) {
        $comp_undefined_courses_array[] = $explode_comp_undefined_courses;
    }

    $inprogress_courses = array_diff($enrolled_courses_array, $comp_undefined_courses_array);


    $table = new html_table();
    $table->attributes = array('class' => 'display');
    $table->head = array(get_string('s_no', 'block_course_status_tracker'), get_string('module', 'block_course_status_tracker'), get_string('course_name', 'block_course_status_tracker'));
    $table->align = array('center', 'left', 'left');
    $table->data = array();
    $i = 0;

    foreach ($inprogress_courses as $course) {
        $row = array();
        $row[] = ++$i;
        $row[] = module_name(get_category_id($course));
        $row[] = "<a href=" . $CFG->wwwroot . "/course/view.php?id=" . $course . ">" . course_name($course) . "</a>";
        $table->data[] = $row;
    }

    return $table;
}

/**
 * This function display list of undefined courses based on login user.
 *
 * @param int   $id user id
 * @return Table table.
 */
function user_undefined_courses_report($userid) {
    global $CFG, $DB;

    $courses = enrol_get_users_courses($userid, false, 'id, shortname, showgrades');
    if ($courses) {
        $table = new html_table();
        $table->attributes = array('class' => 'display');
        $table->head = array(get_string('s_no', 'block_course_status_tracker'), get_string('module', 'block_course_status_tracker'), get_string('course_name', 'block_course_status_tracker'));

        $table->align = array('center', 'left', 'left');
        $table->data = array();
        $i = 0;

        $course_criteria_ns = array();
        static $undefined_courses;
        foreach ($courses as $course) {
            $exist = $DB->record_exists('course_completion_criteria', array('course' => $course->id));
            if (!$exist) {
                $row = array();
                $row[] = ++$i;
                $row[] = module_name($course->category);
                $row[] = "<a href=" . $CFG->wwwroot . "/course/view.php?id=" . $course->id . ">" . course_name($course->id) . "</a>";
                $table->data[] = $row;
            }
        }
    }
    return $table;
}

/**
 * This function count completed course based on login user.
 *
 * @param int   $id user id
 * @return String total course id.
 */
function count_complete_course($userid) {
    global $DB;
    $total_courses = $DB->get_record_sql('SELECT count(course) as total_course FROM {course_completion_crit_compl} WHERE userid = ?', array($userid));
    $total_courses = $total_courses->total_course;
    return $total_courses;
}

/**
 * This function display list of enrolled courses based on login user.
 *
 * @param int   $id user id
 * @return Table table.
 */
function user_enrolled_courses($userid) {
    global $CFG;
    $count_course = 0;
    $courses = enrol_get_users_courses($userid, false, 'id, shortname, showgrades');
    if ($courses) {
        foreach ($courses as $course) {
            $count_course+=1;
        }
    }
    return $count_course;
}

/**
 * This function returns total undefined courses based on login user.
 *
 * @param int   $id user id
 * @return String count number.
 */
function count_course_criteria($userid) {
    global $DB;
    $count = 0;
    $courses = enrol_get_users_courses($userid, false, 'id, shortname, showgrades');
    if ($courses) {
        $course_criteria_ns = array();
        foreach ($courses as $course) {
            $exist = $DB->record_exists('course_completion_criteria', array('course' => $course->id));
            if (!$exist) {
                $count++;
                $course_criteria_ns[] = $course->id;
            }
        }
    }
    return $count;
}

/**
 * This function return course category name based on course id.
 *
 * @param int   $id course id
 * @return String category name.
 */
function module_name($id) {
    global $DB;
    $module = $DB->get_record_sql('SELECT name FROM {course_categories}  WHERE id = ?', array($id));
    $module = format_string($module->name);
    return $module;
}

/**
 * This function returns course name based on course id.
 *
 * @param int   $id course id
 * @return String course name.
 */
function course_name($id) {
    global $DB;
    $course = $DB->get_record_sql('SELECT fullname  FROM {course} WHERE id = ?', array($id));
    $course = format_string($course->fullname);
    $course = $course . ' ' . get_string('course', 'block_course_status_tracker');
    ;
    return $course;
}

/**
 * This function returns user details including user profile picture, name, department and joining date based on login user.
 *
 * @param int   $id user id
 * @return Table table.
 */
function user_details($id) {
    global $OUTPUT, $DB;
    // $user = new stdClass();
    $user = $DB->get_record('user', array('id' => $id));
    //$user->id = $id; // User Id.

    $user->picture = $OUTPUT->user_picture($user, array('size' => 100));
    // Fetch Data.
    $result = $DB->get_record_sql('SELECT concat(firstname," ",lastname) as name,department, timecreated as date  FROM {user} WHERE id = ?', array($id));

    if ($result->date != '0') {
        $date = userdate($result->date, get_string('strftimedate', 'core_langconfig'));
    } else {
        $date = "-";
    }

    $table = '<table width="80%"><tr><td width="20%" style="vertical-align:middle;" rowspan="5">' . $user->picture . '</td></tr>
           <tr><td width="20%">' . get_string('name', 'block_course_status_tracker') . '</td><td>' . $result->name . '</td></tr>';

    $check_designatino_field = report_get_custome_field($id, "Designation"); // Custom Field name for designation is "Designation".
    if ($check_designatino_field != 0) {
        $table .='<tr><td>' . get_string('job_title', 'block_course_status_tracker') . '</td><td>' . format_string($check_designatino_field) . '</td></tr>';
    }
    $table .='<tr><td>' . get_string('department', 'block_course_status_tracker') . '</td><td>' . format_string($result->department) . '</td></tr>
             <tr><td>' . get_string('joining_date', 'block_course_status_tracker') . '</td><td>' . $date . '</td></tr>
             </table>';
    return $table;
}

/**
 * This function returns custom field based on login user and field name.
 *
 * @param int   $id user id, $text field name
 * @return String filed data.
 */
function report_get_custome_field($userid, $text) {
    global $DB;
    $result = $DB->get_record_sql('SELECT table2.data as fieldvalue  FROM {user_info_field} as table1  join  {user_info_data} as table2
                                   on table1.id=table2.fieldid where table2.userid=? AND table1.name=?', array($userid, $text));

    $fieldvalue = $result['fieldvalue'];
    if (empty($fieldvalue)) {
        return "0";
    } else {
        return format_string($result->fieldvalue);
    }
}

/**
 * This function display list of enroled courses based on login user.
 *
 * @param int   $id user id
 * @return Table table.
 */
function user_enrolled_courses_report($userid) {
    global $CFG;
    $count_course = 0;
    $courses = enrol_get_users_courses($userid, false, 'id, shortname, showgrades');
    if ($courses) {
        $table = new html_table();
        $table->attributes = array('class' => 'display');
        $table->head = array(get_string('s_no', 'block_course_status_tracker'), get_string('module', 'block_course_status_tracker'), get_string('course_name', 'block_course_status_tracker'));

        $table->align = array('center', 'left', 'left');
        $table->data = array();
        $i = 0;
        foreach ($courses as $course) {
            $row = array();
            $row[] = ++$i;
            $row[] = module_name($course->category);
            $row[] = "<a href=" . $CFG->wwwroot . "/course/view.php?id=" . $course->id . ">" . course_name($course->id) . "</a>";
            $table->data[] = $row;
        }
    }
    return $table;
}
