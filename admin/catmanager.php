<?php

/*
 * $Id: catmanager.php,v 1.2 2003/09/01 15:05:40 wellwine Exp $
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
require_once sprintf('%s/class/xoopstree.php', XOOPS_ROOT_PATH);

$myts = MyTextSanitizer::getInstance();
$mytree = new XoopsTree($xoopsDB->prefix('weblog_category'), 'cat_id', 'cat_pid');

$action = '';
if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

function &getCategory($post)
{
    $handler = xoops_getModuleHandler('category');

    $cat = $handler->create();

    $cat->setVar('cat_pid', (isset($post['cat_pid'])) ? (int)$post['cat_pid'] : 0);

    $cat->setVar('cat_id', (isset($post['cat_id'])) ? (int)$post['cat_id'] : 0);

    $cat->setVar('cat_created', (isset($post['cat_created'])) ? (int)$post['cat_created'] : 0);

    $cat->setVar('cat_title', $post['title']);

    $cat->setVar('cat_description', $post['desc']);

    $cat->setVar('cat_imgurl', $post['imgurl']);

    return $cat;
}

function catManagerLink()
{
    global $xoopsModule;

    return sprintf(
        '<a href=\'%s/modules/%s/admin/catmanager.php\'>%s</a>',
        XOOPS_URL,
        $xoopsModule->dirname(),
        _AM_WEBLOG_CATMANAGER
    );
}

function catManager()
{
    global $mytree;

    xoops_cp_header();

    echo sprintf(
        '<h4>%s&nbsp;&raquo;&raquo;&nbsp;%s</h4>',
        indexLink(),
        _AM_WEBLOG_CATMANAGER
    );

    echo "<table width='100%' class='outer' cellspacing='1'>\r\n";

    echo sprintf("<tr><th colspan='2'>%s</th></tr>", _AM_WEBLOG_CATMANAGER);

    /****
     * echo sprintf('<tr valign=\'top\' align=\'left\'><form method=\'post\', action=\'catmanager.php\'><td class=\'head\'>%s<br><br>',
     * _AM_WEBLOG_ADDMAINCAT);;
     * echo "<div style='font-weight:normal;'>";
     * echo sprintf('%s: <input type=\'text\' name=\'title\' size=\'30\' maxlength=\'50\'><br>', _AM_WEBLOG_TITLE);
     * echo sprintf('%s:<br><input type=\'text\' name=\'imgurl\' size=\'100\' maxlength=\'150\' value=\'http://\'>', _AM_WEBLOG_IMGURL);
     * echo "<input type=hidden name=cat_pid value='0'>\r\n";
     * echo "<input type=hidden name=desc value=''>\r\n";
     * echo "<input type=hidden name=action value=addCat>\r\n";
     * echo "</dev>";
     * echo "</td>";
     * echo "<td class='even'>\r\n";
     * echo sprintf('<input type=submit value=\'%s\'><br>', _AM_WEBLOG_GO);
     * echo "</td></form></tr>\r\n";
     ****/

    $cathandler = xoops_getModuleHandler('category');

    $count = $cathandler->getCount();

    echo sprintf(
        '<tr valign=\'top\' align=\'left\'><form method=\'post\', action=\'catmanager.php\'><td class=\'head\'>%s<br><br>',
        _AM_WEBLOG_ADDCAT
    );

    echo "<div style='font-weight:normal;'>";

    echo sprintf('%s: <input type=\'text\' name=\'title\' size=\'30\' maxlength=\'50\'><br>', _AM_WEBLOG_TITLE);

    if ($count > 0) {
        echo sprintf('%s: ', _AM_WEBLOG_PCAT);

        $mytree->makeMySelBox('cat_title', 'cat_title', 0, 1, 'cat_pid');
    } else {
        echo "<input type=hidden name=cat_pid value='0'>\r\n";
    }

    echo "<input type=hidden name=desc value=''>\r\n";

    echo "<input type=hidden name=imgurl value=''>\r\n";

    echo "<input type=hidden name=action value=addCat>\r\n";

    echo '</dev>';

    echo '</td>';

    echo "<td class='even'>\r\n";

    echo sprintf('<input type=submit value=\'%s\'><br>', _AM_WEBLOG_GO);

    echo "</td></form></tr>\r\n";

    if ($count > 0) {
        // Modify Category

        echo sprintf(
            '<tr valign=\'top\' align=\'left\'><form method=\'post\', action=\'catmanager.php\'><td class=\'head\'>%s<br><br>',
            _AM_WEBLOG_MODCAT
        );

        echo "<div style='font-weight:normal;'>";

        echo sprintf('%s: ', _AM_WEBLOG_CAT);

        $mytree->makeMySelBox('cat_title', 'cat_title');

        echo "<input type=hidden name=action value=modCat>\r\n";

        echo '</dev>';

        echo '</td>';

        echo "<td class='even'>\r\n";

        echo sprintf('<input type=submit value=\'%s\'><br>', _AM_WEBLOG_GO);

        echo "</td></form></tr>\r\n";
    }

    echo "</table>\r\n";

    xoops_cp_footer();
}

function delCategory($post, $get)
{
    global $xoopsConfig, $xoopsModule;

    $catHandler = xoops_getModuleHandler('category');

    if (!isset($post['ok']) || 1 != $post['ok']) {
        $category = $catHandler->get($get['cat_id']);

        xoops_cp_header();

        xoops_confirm(
            ['action' => 'delCat', 'cat_id' => (int)$get['cat_id'], 'ok' => 1],
            'catmanager.php',
            sprintf(_AM_WEBLOG_DELCONFIRM, $category->getVar('cat_title'))
        );

        xoops_cp_footer();
    } else {
        $entryHandler = xoops_getModuleHandler('entry');

        $id_arr = $catHandler->getAllChildrenIds($post['cat_id']);

        $id_arr[] = $post['cat_id'];

        foreach ($id_arr as $id) {
            $criteria = new criteria('cat_id', $id);

            $entries = $entryHandler->getObjects($criteria);

            foreach ($entries as $entry) {
                if ($entryHandler->delete($entry)) {
                    xoops_comment_delete(
                        $xoopsModule->getVar('mid'),
                        $entry->getVar('blog_id')
                    );

                    xoops_notification_deletebyitem(
                        $xoopsModule->getVar('mid'),
                        'blog_entry',
                        $entry->getVar('blog_id')
                    );
                }
            }

            $category = $catHandler->create();

            $category->setVar('cat_id', $id);

            $catHandler->delete($category);

            /******
             * xoops_notification_deleteitem($xoopsModule->getVar('mid'), 'category', $id);
             ******/
        }

        redirect_header('catmanager.php', 2, _AM_WEBLOG_CATDELETED);

        exit();
    }
}

function addCategory($post)
{
    $cat = getCategory($post);

    if (mb_strlen(trim($cat->getVar('cat_title', 'n'))) < 1) {
        redirect_header('catmanager.php', 2, _AM_WEBLOG_ERRORTITLE);

        exit();
    }

    $cat->setVar('cat_created', time());

    $handler = xoops_getModuleHandler('category');

    $ret = $handler->insert($cat);

    if ($ret) {
        redirect_header('catmanager.php', 2, _AM_WEBLOG_NEWCATADDED);
    } else {
        redirect_header('catmanager.php', 2, _AM_WEBLOG_CATNOTADDED);
    }
}

function modifyCategoryS($post)
{
    $cat = getCategory($post);

    if (mb_strlen(trim($cat->getVar('cat_title', 'n'))) < 1) {
        redirect_header('catmanager.php', 2, _AM_WEBLOG_ERRORTITLE);

        exit();
    }

    $handler = xoops_getModuleHandler('category');

    $ret = $handler->insert($cat);

    if ($ret) {
        redirect_header('catmanager.php', 2, _AM_WEBLOG_CATMODED);
    } else {
        redirect_header('catmanager.php', 2, _AM_WEBLOG_CATNOTMODED);
    }
}

function modifyCategory($post)
{
    global $mytree;

    $cat_id = (isset($post['cat_id'])) ? (int)$post['cat_id'] : 0;

    $handler = xoops_getModuleHandler('category');

    $cat = $handler->get($cat_id);

    xoops_cp_header();

    echo sprintf(
        '<h4>%s&nbsp;&raquo;&raquo;&nbsp;%s&nbsp;&raquo;&raquo;&nbsp;%s</h4>',
        indexLink(),
        catManagerLink(),
        _AM_WEBLOG_MODCAT
    );

    echo "<table width='100%' class='outer' cellspacing='1'>\r\n";

    echo sprintf("<tr><th colspan='2'>%s</th></tr>", _AM_WEBLOG_CATMANAGER);

    echo sprintf(
        '<tr valign=\'top\' align=\'left\'><form method=\'post\', action=\'catmanager.php\'><td class=\'head\'>%s<br><br>',
        _AM_WEBLOG_MODCAT
    );

    echo "<div style='font-weight:normal;'>";

    echo sprintf('%s: \'%s\'<br><br>', _AM_WEBLOG_CHOSECAT, $cat->getVar('cat_title', 's'));

    echo sprintf(
        '%s: <input type=\'text\' name=\'title\' size=\'30\' maxlength=\'50\' value=\'%s\'><br>',
        _AM_WEBLOG_TITLE,
        $cat->getVar('cat_title', 'e')
    );

    /****
     * echo sprintf('%s:<br><input type=\'text\' name=\'imgurl\' size=\'100\' maxlength=\'150\' value=\'%s\'><br>',
     * _AM_WEBLOG_IMGURL, $cat->getVar('cat_imgurl', 's'));
     *****/

    echo sprintf('%s: ', _AM_WEBLOG_PCAT);

    $mytree->makeMySelBox('cat_title', 'cat_title', $cat->getVar('cat_pid'), 1, 'cat_pid');

    echo sprintf('<input type=\'hidden\' name=\'cat_id\' value=\'%d\'>', $cat->getVar('cat_id'));

    echo sprintf('<input type=\'hidden\' name=\'cat_created\' value=\'%d\'>', $cat->getVar('cat_created'));

    echo "<input type='hidden' name='desc' value=''>\r\n";

    echo "<input type='hidden' name='action' value='modCatS'>\r\n";

    echo '</dev>';

    echo '</td>';

    echo "<td class='even'>\r\n";

    echo sprintf('<input type=submit value=\'%s\'>', _AM_WEBLOG_GO);

    echo sprintf(
        '<br><br><input type=button value=\'%s\' onClick="location=\'catmanager.php?cat_pid=%d&amp;cat_id=%d&amp;action=delCat\'">',
        _AM_WEBLOG_DELETE,
        $cat->getVar('cat_pid'),
        $cat->getVar('cat_id')
    );

    echo '<br><br><input type=button value=' . _AM_WEBLOG_CANCEL . " onclick=\"location='catmanager.php'\">";

    echo "</td></form></tr>\r\n";

    echo "</table>\r\n";

    xoops_cp_footer();
}

switch ($action) {
    case 'modCat':
        modifyCategory($_POST);
        break;
    case 'modCatS':
        modifyCategoryS($_POST);
        break;
    case 'addCat':
        addCategory($_POST);
        break;
    case 'delCat':
        delCategory($_POST, $_GET);
        break;
    default:
        catManager();
        break;
}
