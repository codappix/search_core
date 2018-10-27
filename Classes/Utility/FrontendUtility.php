<?php

namespace Codappix\SearchCore\Utility;

/*
 * Copyright (C) 2018 Justus Moroni <developer@leonmrni.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Overwrite BackendUtility to use in frontend.
 * LanguageService was only usable in backend.
 */
class FrontendUtility extends BackendUtility
{
    protected static function getLanguageService(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
