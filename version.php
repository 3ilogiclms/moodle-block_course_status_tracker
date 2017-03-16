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
$plugin->version = 2017031600;        // The current plugin version (Date: YYYYMMDDXX)
$plugin->requires = 2013040500;        // Requires this Moodle version
$plugin->release = '3.2';
$plugin->component = 'block_course_status_tracker';
$plugin->maturity = MATURITY_STABLE;
