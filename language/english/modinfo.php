<?php
/**
 * $Id: modinfo.php,v 1.4 2003/10/26 08:28:42 wellwine Exp $
 * Copyright (c) 2003 by Jeremy N. Cowgar <jc@cowgar.com>
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
define('_MI_WEBALIZER_NAME', 'webalizer');
define('_MI_WEBALIZER_DESC', 'Xoops wrapper for Webalizer');

// Config Settings
define('_MI_WEBALIZER_OUT_DIR', 'Webalizer Output Directory');
define('_MI_WEBALIZER_OUT_DIRDESC', 'The directory where webalizer writes its output.<br>This should be the same value as the OutputDir setting in your webalizer.conf file.');
define('_MI_WEBALIZER_START', 'Start Page');
define('_MI_WEBALIZER_STARTDESC', 'The webalizer page to show on the index.<br>The default should be fine for most people. You\'d only change this if you have setup webalizer to use a differenc extension (for example .php) and your main index was named differently.');
define('_MI_WEBALIZER_TEMPLATE_ENTRIESDSC', 'Index for the webalizer');
