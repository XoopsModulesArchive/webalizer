<?php

/*
 * $Id: dbmanager.php,v 1.2 2003/10/26 08:28:42 wellwine Exp $
 * Copyright (c) 2003 by Hiro SAKAI (http://wellwine.net/)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting
 * source code which is considered copyrighted (c) material of the
 * original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
 */
require dirname(__DIR__, 3) . '/mainfile.php';
include sprintf('%s/include/cp_header.php', XOOPS_ROOT_PATH);
require_once sprintf('%s/modules/%s/config.php', XOOPS_ROOT_PATH, $xoopsModule->dirname());
require __DIR__ . '/admin.inc.php';

$action = '';
if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}

function dbManagerLink()
{
    global $xoopsModule;

    return sprintf(
        '<a href=\'%s/modules/%s/admin/dbmanager.php\'>%s</a>',
        XOOPS_URL,
        $xoopsModule->dirname(),
        _AM_WEBLOG_DBMANAGER
    );
}

function dbManager()
{
    xoops_cp_header();

    echo sprintf(
        '<h4>%s&nbsp;&raquo;&raquo;&nbsp;%s</h4>',
        indexLink(),
        _AM_WEBLOG_DBMANAGER
    );

    echo "<table width='100%' class='outer' cellspacing='1'>\r\n";

    echo sprintf("<tr><th colspan='2'>%s</th></tr>", _AM_WEBLOG_DBMANAGER);

    // synchronize # of comments

    echo tableRow(_AM_WEBLOG_SYNCCOMMENTS, _AM_WEBLOG_SYNCCOMMENTSDSC, 'comments');

    // check table

    echo tableRow(_AM_WEBLOG_CHECKTABLE, _AM_WEBLOG_CHECKTABLEDSC, 'checktable');

    echo "</table>\r\n";

    xoops_cp_footer();
}

/**
 * param[0]=table name
 * param[1]=column name
 * @param mixed $post
 */
function addColumn($post)
{
    global $xoopsDB;

    $table = $post['param'][0];

    $column = $post['param'][1];

    if ('weblog' == $table) {
        if ('cat_id' == $column) {
            $sql = sprintf(
                'ALTER TABLE %s ADD cat_id INT( 5 ) UNSIGNED DEFAULT \'1\' NOT NULL',
                $xoopsDB->prefix('weblog')
            );
        } elseif ('dohtml' == $column) {
            $sql = sprintf(
                'ALTER TABLE %s ADD dohtml TINYINT( 1 ) DEFAULT \'0\' NOT NULL',
                $xoopsDB->prefix('weblog')
            );
        } else {
            redirect_header('dbmanager.php', 2, _AM_WEBLOG_UNSUPPORTED);

            exit();
        }

        $result = $xoopsDB->query($sql);

        if (!$result) {
            redirect_header('dbmanager.php', 5, sprintf(_AM_WEBLOG_COLNOTADDED, $xoopsDB->error()));

            exit();
        }  

        redirect_header('dbmanager.php', 2, _AM_WEBLOG_COLADDED);

        exit();
    }  

    redirect_header('dbmanager.php', 2, _AM_WEBLOG_UNSUPPORTED);

    exit();
}

function addTable($post)
{
    global $xoopsDB;

    $table = $post['param'][0];

    if ('weblog_category' == $table) {
        $sql = sprintf('CREATE TABLE %s (', $xoopsDB->prefix('weblog_category'));

        $sql .= 'cat_id int(5) unsigned NOT NULL auto_increment,';

        $sql .= 'cat_pid int(5) unsigned NOT NULL default \'0\',';

        $sql .= 'cat_title varchar(50) NOT NULL default \'\',';

        $sql .= 'cat_description text NOT NULL,';

        $sql .= 'cat_created int(10) NOT NULL default \'0\',';

        $sql .= 'cat_imgurl varchar(150) NOT NULL default \'\',';

        $sql .= 'PRIMARY KEY  (cat_id),';

        $sql .= 'KEY cat_pid (cat_pid)';

        $sql .= ') ENGINE = ISAM;';
    } elseif ('weblog_priv' == $table) {
        $sql = sprintf('CREATE TABLE %s(', $xoopsDB->prefix('weblog_priv'));

        $sql .= 'priv_id smallint(5) unsigned NOT NULL auto_increment,';

        $sql .= 'priv_gid smallint(5) unsigned NOT NULL default \'0\',';

        $sql .= 'PRIMARY KEY  (priv_id)';

        $sql .= ') ENGINE = ISAM;';
    } else {
        redirect_header('dbmanager.php', 2, _AM_WEBLOG_UNSUPPORTED);

        exit();
    }

    $result = $xoopsDB->query($sql);

    if (!$result) {
        redirect_header('dbmanager.php', 5, sprintf(_AM_WEBLOG_TABLENOTADDED, $xoopsDB->error()));

        exit();
    }  

    if ('weblog_category' == $table) {
        $handler = xoops_getModuleHandler('category');

        $cat = $handler->create();

        $cat->setVar('cat_pid', 0);

        $cat->setVar('cat_id', 1);

        $cat->setVar('cat_created', time());

        $cat->setVar('cat_title', 'Miscellaneous');

        $cat->setVar('cat_description', '');

        $cat->setVar('cat_imgurl', '');

        $ret = $handler->insert($cat);
    }

    redirect_header('dbmanager.php', 5, _AM_WEBLOG_TABLEADDED);

    exit();
}

function checkTables()
{
    xoops_cp_header();

    echo sprintf(
        '<h4>%s&nbsp;&raquo;&raquo;&nbsp;%s&nbsp;&raquo;&raquo;&nbsp;%s</h4>',
        indexLink(),
        dbManagerLink(),
        _AM_WEBLOG_CHECKTABLE
    );

    // checking table 'weblog'

    $columns = [
        'blog_id',
        'user_id',
        'cat_id',
        'created',
        'title',
        'contents',
        'private',
        'comments',
        'reads',
        'dohtml',
    ];

    checkTable('weblog', $columns);

    echo '<br>';

    // checking table 'weblog_category'

    $columns = ['cat_id', 'cat_pid', 'cat_title', 'cat_description', 'cat_created', 'cat_imgurl'];

    checkTable('weblog_category', $columns);

    echo '<br>';

    // checking table 'weblog_priv'

    $columns = ['priv_id', 'priv_gid'];

    checkTable('weblog_priv', $columns);

    xoops_cp_footer();
}

function checkTable($table, $columns = [])
{
    global $xoopsDB;

    $sql = sprintf(
        'SELECT count(*) AS count FROM %s WHERE 1',
        $xoopsDB->prefix($table)
    );

    $result = $xoopsDB->query($sql);

    $table_exist = ($result) ? true : false;

    if ($table_exist) {
        [$count] = $xoopsDB->fetchRow($result);

        $row_exist = ($count['count'] > 0) ? true : false;
    }

    echo "<table width='100%' class='outer' cellspacing='1'>\r\n";

    echo sprintf('<tr><th colspan=\'2\'>%s: \'%s\'</th></tr>', _AM_WEBLOG_TABLE, $table);

    // if table does not exist or table does not have rows

    //if (!$table_exist || !$row_exist) {

    if (!$table_exist) {
        $hidden = [0 => $table];

        echo tableRow(
            sprintf(_AM_WEBLOG_CREATETABLE, $table),
            sprintf(_AM_WEBLOG_CREATETABLEDSC, $table),
            'addtable',
            $hidden
        );

    // table does exist and columns are missing
    } else {
        $sql = sprintf('SHOW COLUMNS FROM %s', $xoopsDB->prefix($table));

        $result = $xoopsDB->query($sql);

        $fields = [];

        while (list($field) = $xoopsDB->fetchRow($result)) {
            $fields[] = $field;
        }

        $alter = false;

        foreach ($columns as $column) {
            foreach ($fields as $field) {
                if ($column === $field) {
                    continue 2;
                }
            }

            $hidden = [0 => $table, 1 => $column];

            echo tableRow(sprintf(_AM_WEBLOG_ADD, $column), sprintf(_AM_WEBLOG_ADDDSC, $column), 'addcolumn', $hidden);

            $alter = true;
        }

        if (false === $alter) {
            echo tableRow(sprintf(_AM_WEBLOG_NOADD, $table), sprintf(_AM_WEBLOG_NOADDDSC, $table));
        }
    }

    echo "</table>\r\n";
}

function synchronizeComments()
{
    global $xoopsDB, $xoopsModule;

    $sql = sprintf(
        'SELECT bl.blog_id, COUNT(cm.com_id) FROM %s AS bl LEFT JOIN %s AS cm ON bl.blog_id=cm.com_itemid AND cm.com_modid=%d GROUP BY bl.blog_id',
        $xoopsDB->prefix('weblog'),
        $xoopsDB->prefix('xoopscomments'),
        $xoopsModule->getVar('mid')
    );

    $result = $xoopsDB->query($sql) || exit($xoopsDB->error());

    $handler = xoops_getModuleHandler('entry');

    while (list($blog_id, $comments) = $xoopsDB->fetchRow($result)) {
        $handler->updateComments($blog_id, (int)$comments);
    }

    redirect_header('dbmanager.php', 2, _AM_WEBLOG_DBUPDATED);

    exit();
}

switch ($action) {
    case 'comments':
        synchronizeComments();
        break;
    case 'checktable':
        checkTables();
        break;
    case 'addcolumn':
        addColumn($_POST);
        break;
    case 'addtable':
        addTable($_POST);
        break;
    default:
        dbManager();
        break;
}
