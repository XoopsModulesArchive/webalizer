<?php
/*
 * $Id: index.php,v 1.6 2003/10/30 12:34:52 wellwine Exp $
 * Copyright (c) 2003 by Jeremy N. Cowgar <jc@cowgar.com>
 * Copyright (c) 2003 by Hiro SAKAI (http://wellwine.zive.net/)
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

require __DIR__ . '/header.php';

// obtain configuration parameters
//$perPage = $xoopsModuleConfig['numperpage'];

// Determine the user we are retrieving the blog entries for
if (is_object($xoopsUser)) {
    $currentUser = $xoopsUser;
} else {
    $currentUser = new XoopsUser();

    $currentUser->setVar('uid', 0);
}
$isAdmin = $currentUser->isAdmin($xoopsModule->mid());
$currentuid = $currentUser->getVar('uid');

$outDir = $xoopsModuleConfig['webalizer_out_dir'];
$defaultIndex = $xoopsModuleConfig['webalizer_start'];
$id = !empty($_GET['id']) ? $_GET['id'] : $defaultIndex;
$file = "$outDir/$id";

if (preg_match("/\.png$/i", $id)) {
    header('Content-Type: image/png');

    readfile($file);
} else {
    // Include the page header

    require XOOPS_ROOT_PATH . '/header.php';

    $output = 0;

    $ih = fopen($file, 'rb');

    if (false === $ih) {
        ?>
        Error! I couldn't open the webalizer output index.
        <p>
        Check your configuration and make sure it is pointing to the directory where
        webalizer output is stored.
        If you use a webalizer.conf file this would be the same value as
        your OutputDir setting.
        <?php
        return;
    }

    while (!feof($ih)) {
        $line = fgets($ih, 4096);

        if ($output) {
            if (preg_match('/</BODY/i', $line)) {
                $output = 0;
            } else {
                // rewrite images

                $line = preg_replace(
                    '/SRC="([^"]+)"/i',
                    'SRC="index.php?id=$1"',
                    $line
                );

                // rewrite links

                $line = preg_replace(
                    '/HREF="([^(#|http)][^"]+)"/i',
                    'HREF="index.php?id=$1"',
                    $line
                );

                echo $line;
            }
        } else {
            if (preg_match('/<BODY/i', $line)) {
                $output = 1;
            }
        }
    }

    fclose($ih);

    // Include the page footer

    require XOOPS_ROOT_PATH . '/footer.php';
}

?>
